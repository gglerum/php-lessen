<?php

namespace App\Actions\Answer;

use Illuminate\Support\Str;
use App\Models\Question;

class UploadAction extends AnswerAction
{
    public function canHandle(Question $question): bool
    {
        // Check if the question type is 'file_upload'
        return $question->type === 'file_upload';
    }

    public function handle(Question $question, array $data): void
    {
        $given_answer = $this->answerQuestion($question->id, true);
        $storedFiles = $this->storeFiles($data);
        $given_answer->files()->createMany($storedFiles);
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
        return array_map(function ($file): array {
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
}
