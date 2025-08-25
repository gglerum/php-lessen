<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreFileRequest;
use App\Services\AnswerService;
use App\Models\User;
use App\Models\Question;
use App\ValueObjects\FileSize;
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

        $answerService->answerQuestion(
            Question::find($validatedData['questionId']),
            $request->file('files')
        );

        $user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));
    }
}
