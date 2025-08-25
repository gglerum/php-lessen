<?php

namespace App\Actions\Answer;

use App\Models\GivenAnswer;
use App\Models\User;
use App\Models\Question;
use App\Contracts\AnswerAction as AnswerActionContract;
use Illuminate\Container\Attributes\CurrentUser;

abstract class AnswerAction implements AnswerActionContract
{
    /**
     * Constructor to initialize the action with the current user.
     */
    public function __construct(#[CurrentUser] private User $user, private Application $application) {}

    /**
     * Check if the action can handle the given question type.
     *
     * @param \App\Models\Question $question
     * @return bool
     */
    abstract public function canHandle(Question $question): bool;

    /**
     * Handle the action for the given question and data.
     *
     * @param \App\Models\Question $question
     * @param array $data
     * @param \App\Models\User $user
     */
    abstract public function handle(Question $question, array $data): void;

    /**
     * Updates or creates an answer for a specific question in an application
     *
     * @param bool $answer The boolean answer to the question
     * @return GivenAnswer The created or updated answer record
     */
    protected function answerQuestion(int $questionId, bool $answer): GivenAnswer
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
}
