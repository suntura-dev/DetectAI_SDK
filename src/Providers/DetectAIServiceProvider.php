<?php

namespace DetectAI\Providers;

use OpenAI;
use Illuminate\Support\ServiceProvider;

class DetectAIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(OpenAI::class, function ($app) {
            return new OpenAI([
                'api_key' => config('detect-ai.openai.key'),
            ]);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
