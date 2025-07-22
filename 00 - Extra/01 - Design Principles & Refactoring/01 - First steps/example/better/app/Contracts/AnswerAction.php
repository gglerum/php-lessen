<?php

namespace App\Contracts;

use App\Models\Question;
use App\Models\User;

interface AnswerAction
{
    /**
     * Determine if the handler can process the given question.
     *
     * @param Question $question The question to check
     * @return bool True if the handler can process the question, false otherwise
     */
    public function canHandle(Question $question): bool;
    /**
     * Handle the answering of a question with the provided data.
     *
     * @param Question $question The question being answered
     * @param array $data The data containing the answer and any files
     * @param User $user The user who is answering the question
     * @return void
     */
    public function handle(Question $question, array $data): void;
}
