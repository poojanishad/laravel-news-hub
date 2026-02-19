<?php

namespace App\Services\NewsProviders;

use App\Contracts\NewsProviderInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery\Exception\RuntimeException;

class GNewsProvider implements NewsProviderInterface
{
    public function name(): string
    {
        return 'gnews';
    }

    public function fetch(): array
    {
        Log::info('Fetching articles', ['provider' => $this->name()]);

        $endpoint = config('services.gnews.endpoint');

        if (! $endpoint) {
            throw new RuntimeException('GNews endpoint missing in config/services.php');
        }

        Log::info('Fetching articles', ['provider' => $this->name()]);

        $response = Http::timeout(10)
            ->retry(3, 500)
            ->get($endpoint, [
                'category' => config('services.gnews.category'),
                'lang'     => config('services.general.language'),
                'country'  => config('services.general.country'),
                'max'      => config('services.general.max'),
                'apikey'   => config('services.gnews.key'),
            ]);

        if ($response->failed()) {
            throw new RuntimeException(
                'GNews API failed with status ' . $response->status()
            );
        }

        $articles = collect($response->json('articles', []))
            ->filter(fn($article) => !empty($article['url']))
            ->map(function (array $article) {

                return [
                    'title'        => $article['title'] ?? 'Untitled Article',
                    'description'  => $article['description'] ?? str($article['content'] ?? '')->limit(150, '...')->toString(),
                    'content'      => $article['content'] ?? $article['description'] ?? null,
                    'author'       => $article['source']['name'] ?? null,
                    'source'       => 'GNews',
                    'category'     => config('news.gnews.category'),
                    'url'          => $article['url'] ?? null,
                    'image_url'    => $article['image'] ?? null,
                    'published_at' => isset($article['publishedAt']) ? Carbon::parse($article['publishedAt'])->toDateTimeString() : now()->toDateTimeString(),
                ];
            })
            ->values()
            ->toArray();

        Log::info('Articles fetched', [
            'provider' => $this->name(),
            'count'    => count($articles),
        ]);

        return $articles;
    }
}