<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Application;
use App\Services\AnswerService;
use App\Actions\Answer\UploadAction;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->app->bind(Application::class, function () {
            $applicationId = request('applicationId') ?: request()->route('application');
            return Application::findOrFail($applicationId);
        });

        $this->app->singleton(AnswerService::class, function ($app) {
            return new AnswerService(
                collect(
                    array_map(fn($handler) => $app->make($handler), config('answer.handlers', []))
                )
            );
        });
    }
}
