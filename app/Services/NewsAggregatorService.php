<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

class NewsAggregatorService
{
    public function __construct(private array $providers) {}

    public function fetchAndStore(): void
    {
        foreach ($this->providers as $provider) {
            try {
                Log::info('Running provider', [
                    'provider' => get_class($provider)
                ]);

                $articles = $provider->fetch();

                foreach ($articles as $article) {
                    \App\Models\Article::updateOrCreate(
                        ['url' => $article['url']],
                        $article
                    );
                }

            } catch (\Throwable $e) {
                Log::error('Provider crashed', [
                    'provider' => get_class($provider),
                    'error' => $e->getMessage()
                ]);

                continue;
            }
        }
    }
}

