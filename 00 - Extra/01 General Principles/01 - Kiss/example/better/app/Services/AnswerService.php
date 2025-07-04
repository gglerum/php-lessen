<?php

namespace App\Services;

use App\Models\GivenAnswer;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Application;
use Illuminate\Container\Attributes\CurrentUser;

/**
 * Service class to handle answering questions in an application
 */
class AnswerService
{
    public function __construct(#[CurrentUser] private User $user, private Application $application) {}

    /**
     * Updates or creates an answer for a specific question in an application
     *
     * @param bool $answer The boolean answer to the question
     * @return GivenAnswer The created or updated answer record
     */
    private function answerQuestion(int $questionId, bool $answer): GivenAnswer
    {
        return GivenAnswer::updateOrCreate(
            [
                'application_id' => $this->application->id,
                'question_id' => $questionId,
            ],
            [
                'answer' => $answer
            ]
        );
    }

    /**
     * Store multiple files and return their metadata as an array.
     *
     * @param array $files Array of UploadedFile instances to be stored
     * @return array Array of stored file metadata containing:
     *               - filename: Original name of the file
     *               - extension: Original file extension
     *               - path: Storage path of the file
     *               - uuid: Generated unique identifier
     *               - user_id: ID of the user who uploaded the file
     */
    private function storeFiles(array $files): array
    {
        return array_map(function ($file) {
            $filePath = '/' . $file->store('uploaded_files/' . $this->application->id, 'local');

            return [
                'filename' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'path' => $filePath,
                'uuid' => Str::uuid(),
                'user_id' => $this->user->id
            ];
        }, $files);
    }

    /**
     * Processes a question answer along with associated files
     * 
     * This method handles answering a question when files are involved by:
     * 1. Creating an answer record
     * 2. Storing the uploaded files
     * 3. Associating the stored files with the answer
     *
     * @param array $files Array of uploaded files to be processed and stored
     * @return void
     */
    public function answerQuestionWithFiles(int $questionId, array $files): void
    {
        $given_answer = $this->answerQuestion($questionId, true);
        $storedFiles = $this->storeFiles($files);
        $given_answer->files()->createMany($storedFiles);
    }
}
