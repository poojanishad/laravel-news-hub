<?php

namespace App\Services\NewsProviders;

use App\Contracts\NewsProviderInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsDataProvider implements NewsProviderInterface
{
    public function fetch(): array
    {
        Log::info('Fetching from NewsDataProvider');

        $response = Http::timeout(10)
            ->retry(3, 200)
            ->get(config('services.newsdata.endpoint'), [
                'apikey'  => config('services.newsdata.key'),
                'language'=> config('services.general.language'),
            ]);

        if ($response->failed()) {
            Log::error('NewsDataProvider failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [];
        }

        $articles = collect($response->json('results', []))
            ->filter(fn ($a) => !empty($a['link']))
            ->map(fn ($a) => $this->transform($a))
            ->unique('url')
            ->values()
            ->toArray();

        Log::info('NewsDataProvider fetched articles', [
            'count' => count($articles),
        ]);

        return $articles;
    }

    protected function transform(array $article): array
    {
        return [
            'title'       => $article['title'] ?? 'Untitled Article',
            'description' => $article['description'] ?? Str::limit(strip_tags($article['content'] ?? ''), 150),
            'content'     => $article['content'] ?? null,
            'author'      => $article['creator'][0] ?? null,
            'source'      => 'NewsData',
            'category'    => $article['category'][0] ?? null,
            'url'         => $article['link'],
            'image_url'   => $article['image_url'] ?? null,
            'published_at' => isset($article['pubDate']) ? Carbon::parse($article['pubDate'])->toDateTimeString() : now()->toDateTimeString(),
        ];
    }
}
