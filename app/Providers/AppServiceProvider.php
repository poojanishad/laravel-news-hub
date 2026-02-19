<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\NewsProviders\{
    NewsProviderFactory,
    GNewsProvider,
    NewsApiProvider,
    NewsDataProvider,
    TheGuardianProvider
};

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(NewsProviderFactory::class, function ($app) {
            return new NewsProviderFactory([
                'gnews' => $app->make(GNewsProvider::class),
                'newsapi' => $app->make(NewsApiProvider::class),
                'newsdata' => $app->make(NewsDataProvider::class),
                'guardian' => $app->make(TheGuardianProvider::class),
            ]);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
