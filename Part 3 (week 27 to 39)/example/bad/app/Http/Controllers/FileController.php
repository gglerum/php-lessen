<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Http\Requests\StoreFileRequest;
use App\Models\GivenAnswer;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class FileController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFileRequest $request)
    {
        /** @var User */
        $user = auth()->user();

        // MISTAKE 1: overly complicated logic to get the application. If `$request->applicationId` is always provided, than we don't
        // have to check if the application is the same as the one in the user model. Because accordign to the logic the one in the request
        // is always the one that should be used.
        $application = $user->application;

        if ($application->id != $request->applicationId) {
            $application = $application->where('id', $request->applicationId)->first();
        }
        // END MISTAKE 1

        // MISTAKE 2: Authorization logic belongs in the StoreFileRequest class.
        Gate::authorize('update', [$application, $user->organisation->id]);
        // END MISTAKE 2

        // MISTAKE 3: For some reason additional validation logic is added here, which should be added to the rules in the
        // StoreFileRequest class.
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'mimes:pdf|min:1|max:5120',
        ]);
        //END MISTAKE 3

        // MISTAKE 4: This logic is to check if the upload limit has been exceeded, but it should be done in the StoreFileRequest class
        // with the rest of the validation logic.

        //calculated total attachment size
        $totalUploadedSize = array_reduce($request->file('files'), fn(int $carry, $file) => $carry += $file->getSize(), 0);

        //check if uploadlimit has been exceeded
        if ($user->getUploadLimit() < $totalUploadedSize + $user->getTotalUploadSize()) {
            throw ValidationException::withMessages(['max_upload_size' => 'Attachments file size exceeds upload limit.']);
        }
        //END MISTAKE 4

        // MISTAKE 5: The logic to update or create the answer should be moved to a service class, like AnswerService.
        // business logic does not belong in the controller.

        foreach ($request->file('files') as $file) {
            $filePath = '/' . $file->store('uploaded_files/' . $application->id, 'local');

            // MISTAKE 6: the application id and question id stay the same
            // so for every file a needless query is made to the database.
            // This should be done once per question, not per file.
            $given_answer = GivenAnswer::updateOrCreate(
                [
                    'application_id' => $application->id,
                    'question_id' => $request->questionId,
                ],
                [
                    'answer' => true
                ]
            );

            // MISTAKE 7: for each file a separate query is made to the database to update or create the file record.
            // This can be optimized by storing all files in one query.
            // further more there is no logic to replace a file on disk, so there is no use updating the file in the database.
            // Especially since all the attributes are compared, and uuid is unique for each file.
            // This means that if the file is already in the database, it will not be updated
            // A better approach would be to create a new file record for each uploaded file, without checking if it already exists.
            $given_answer->files()->updateOrCreate([
                'filename' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'path' => $filePath,
                'uuid' => Str::uuid(),
                'user_id' => $user->id
            ]);
        }

        //update uploaded size total in database
        $user->updateUploadSizeTotal($totalUploadedSize);
    }
}
