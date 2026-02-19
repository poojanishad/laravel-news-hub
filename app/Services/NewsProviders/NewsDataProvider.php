<?php

namespace App\Services\NewsProviders;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class NewsDataProvider extends BaseNewsProvider
{
    public function name(): string
    {
        return 'newsdata';
    }

    public function fetch(): array
    {
        Log::info('Fetching articles', ['provider' => $this->name()]);

        if (!config('services.newsdata.endpoint')) {
            throw new \RuntimeException('NewsData endpoint missing in config/services.php');
        }

        $data = $this->get(config('services.newsdata.endpoint'), [
            'apikey'  => config('services.newsdata.key'),
            'language'=> config('services.general.language'),
        ]);

        $articles = collect($data['results'] ?? [])
            ->filter(fn ($article) => !empty($article['link']))
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
            'content'      => $article['content'] ?? null,
            'author'       => $article['creator'][0] ?? 'Unknown',
            'source'       => $this->name(),
            'category'     => $article['category'][0] ?? null,
            'url'          => $article['link'],
            'image_url'    => $article['image_url'] ?? null,
            'published_at' => isset($article['pubDate'])
                ? Carbon::parse($article['pubDate'])->toDateTimeString()
                : now()->toDateTimeString(),
        ];
    }
}
