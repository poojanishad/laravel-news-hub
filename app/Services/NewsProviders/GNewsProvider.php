<?php

namespace App\Services\NewsProviders;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class GNewsProvider extends BaseNewsProvider
{
    public function name(): string
    {
        return 'gnews';
    }

    public function fetch(): array
    {
        Log::info('Fetching articles from', ['provider' => $this->name()]);

        if (!config('services.gnews.endpoint')) {
            throw new \RuntimeException('GNews endpoint missing in config/services.php');
        }

        Log::info('Fetching articles', ['provider' => $this->name()]);

        $data = $this->get(config('services.gnews.endpoint'), [
            'apikey' => config('services.gnews.key'),
            'lang'   => config('services.general.language'),
            'max'    => 20,
        ]);

        $articles = collect($data['articles'] ?? [])
            ->filter(fn($article) => !empty($article['url']))
            ->map(function (array $article) {
                return [
                    'title'        => $article['title'] ?? 'Untitled Article',
                    'description'  => $article['description']
                        ?? str($article['content'] ?? '')->limit(150)->toString(),
                    'content'      => $article['content'] ?? $article['description'] ?? null,
                    'author'       => $article['source']['name'] ?? null,
                    'source'       => $this->name(),
                    'category'     => config('news.gnews.category'),
                    'url'          => $article['url'],
                    'image_url'    => $article['image'] ?? null,
                    'published_at' => isset($article['publishedAt'])
                        ? Carbon::parse($article['publishedAt'])->toDateTimeString()
                        : now()->toDateTimeString(),
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