<?php

namespace App\Services;

use App\Models\Article;
use App\Services\NewsProviders\NewsProviderFactory;

class NewsAggregatorService
{
    protected $factory;

    protected $providers = [
        'newsapi',
        'gnews',
        'newsdata',
        'guardian',
    ];

    public function __construct(NewsProviderFactory $factory)
    {
        $this->factory = $factory;
    }

    public function fetchFromAll(): void
    {
        foreach ($this->providers as $providerName) {
            $this->fetchAndStore($providerName);
        }
    }

    public function fetchFromSingle(string $providerName): void
    {
        $this->fetchAndStore($providerName);
    }

    protected function fetchAndStore(string $providerName): void
    {
        $provider = $this->factory->make($providerName);

        $articles = $provider->fetch();

        if (!is_array($articles)) {
            throw new \Exception("Provider {$providerName} did not return valid array.");
        }

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

    public function paginate($query, $perPage = 10)
    {
        $paginator = $query->latest()->paginate($perPage);

        return [
            'data' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
            ]
        ];
    }

}
