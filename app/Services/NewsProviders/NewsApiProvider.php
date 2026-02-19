<?php

namespace App\Services\NewsProviders;

use App\Contracts\NewsProviderInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsApiProvider implements NewsProviderInterface
{
    public function fetch(): array
    {
        Log::info('Fetching from NewsApiProvider');

        $response = Http::timeout(10)
            ->retry(3, 200)
            ->get(config('services.newsapi.endpoint'), [
                'q'        => config('services.newsapi.query', 'news'),
                'language' => config('services.general.language'),
                'pageSize' => config('services.general.pageSize'),
                'apiKey'  => config('services.newsapi.key'),
            ]);

        if ($response->failed()) {
            Log::error('NewsApiProvider failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [];
        }

        $articles = collect($response->json('articles', []))
            ->filter(fn ($a) => !empty($a['url']))
            ->map(fn ($a) => $this->transform($a))
            ->unique('url')
            ->values()
            ->toArray();

        Log::info('NewsApiProvider fetched articles', [
            'count' => count($articles),
        ]);

        return $articles;
    }

    protected function transform(array $article): array
    {
        return [
            'title' => $article['title']
                ?? 'Untitled Article',

            'description' => $article['description']
                ?? Str::limit(strip_tags($article['content'] ?? ''), 150),

            'content' => $article['content']
                ?? $article['description']
                ?? null,

            'author' => $article['author']
                ?? ($article['source']['name'] ?? 'Unknown'),

            'source' => 'NewsAPI',

            'category' => config('services.newsapi.category'),

            'url' => $article['url'],

            'image_url' => $article['urlToImage']
                ?? null,

            'published_at' => isset($article['publishedAt'])
                ? Carbon::parse($article['publishedAt'])->toDateTimeString()
                : now()->toDateTimeString(),
        ];
    }
}

