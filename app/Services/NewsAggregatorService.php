<?php

namespace App\Services;

use App\Models\Article;
use App\Services\NewsProviders\NewsProviderFactory;

class NewsAggregatorService
{
    protected array $providers = [
        'gnews',
        'newsapi',
        'newsdata',
        'guardian',
    ];

    public function __construct(
        protected NewsProviderFactory $factory
    ) {}

    public function fetchFromAll(): void
    {
        foreach ($this->providers as $provider) {
            $this->fetchFromSingle($provider);
        }
    }

    public function fetchFromSingle(string $provider): void
    {
        $providerInstance = $this->factory->make($provider);

        $articles = $providerInstance->fetch();

        $this->store($articles);
    }

    protected function store(array $articles): void
    {
        foreach ($articles as $article) {
            if (!isset($article['url'])) {
                continue;
            }

            Article::updateOrCreate(
                ['url' => $article['url']],
                $article
            );
        }
    }
}