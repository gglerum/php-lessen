<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Services\AnswerService;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;

class FileController extends Controller
{
    /**
     * Store files for a specific question in an application.
     *
     * @param StoreFileRequest $request
     * @param User $user
     * @param AnswerService $answerService
     * @return void
     */
    public function store(StoreFileRequest $request, #[CurrentUser] User $user, AnswerService $answerService)
    {
        $validatedData = $request->validated();

        $answerService->answerQuestionWithFiles(
            $validatedData['questionId'],
            $request->file('files')
        );

        $user->updateUploadSizeTotal($validatedData['total_file_size']);
    }
}
