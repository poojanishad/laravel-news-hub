<?php

namespace App\Services\NewsProviders;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class NewsApiProvider extends BaseNewsProvider
{
    public function name(): string
    {
        return 'newsapi';
    }

    public function fetch(): array
    {
        Log::info('Fetching articles', ['provider' => $this->name()]);

        if (!config('services.newsapi.endpoint')) {
            throw new \RuntimeException('NewsAPI endpoint missing in config/services.php');
        }

        $data = $this->get(config('services.newsapi.endpoint'), [
            'q'        => config('services.newsapi.query', 'news'),
            'language' => config('services.general.language'),
            'pageSize' => config('services.general.pageSize', 20),
            'apiKey'   => config('services.newsapi.key'),
        ]);

        $articles = collect($data['articles'] ?? [])
            ->filter(fn ($article) => !empty($article['url']))
            ->map(fn ($article) => $this->transform($article))
            ->unique('url')
            ->values()
            ->toArray();

        Log::info('Articles fetched', [
            'provider' => $this->name(),
            'count'    => count($articles),
        ]);

        return $articles;
    }

    protected function transform(array $article): array
    {
        return [
            'title'        => $article['title'] ?? 'Untitled Article',
            'description'  => $article['description']
                ?? str(strip_tags($article['content'] ?? ''))->limit(150)->toString(),
            'content'      => $article['content']
                ?? $article['description']
                ?? null,
            'author'       => $article['author']
                ?? ($article['source']['name'] ?? 'Unknown'),
            'source'       => $this->name(),
            'category'     => config('services.newsapi.category'),
            'url'          => $article['url'],
            'image_url'    => $article['urlToImage'] ?? null,
            'published_at' => isset($article['publishedAt'])
                ? Carbon::parse($article['publishedAt'])->toDateTimeString()
                : now()->toDateTimeString(),
        ];
    }
}
