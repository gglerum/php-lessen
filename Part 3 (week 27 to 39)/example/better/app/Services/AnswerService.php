<?php

namespace App\Services;

use App\Models\Question;
use Illuminate\Support\Collection;

/**
 * Service class to handle answering questions in an application
 */
class AnswerService
{
    public function __construct(private Collection $handlers) {}

    /**
     * Answer a question with the provided data.
     *
     * @param Question $question The question to answer
     * @param array $data The data to use for answering the question
     * @return void
     */
    public function answerQuestion(Question $question, array $data): void
    {
        $this->handlers->first(fn($h) => $h->canHandle($question))
            ->handle($question, $data);
    }
}
