<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Application;

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
    }
}
