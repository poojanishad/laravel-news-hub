<?php

namespace App\Services\NewsProviders;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TheGuardianProvider extends BaseNewsProvider
{
    public function name(): string
    {
        return 'guardian';
    }

    public function fetch(): array
    {
        Log::info('Fetching articles', ['provider' => $this->name()]);

        if (!config('services.guardian.endpoint')) {
            throw new \RuntimeException('The Guardian endpoint missing in config/services.php');
        }

        $data = $this->get(config('services.guardian.endpoint'), [
            'from-date'   => config('services.guardian.from_date', '2024-01-01'),
            'page-size'   => config('services.general.pageSize'),
            'show-fields' => 'thumbnail,bodyText',
            'show-tags'   => 'contributor',
            'api-key'     => config('services.guardian.key'),
        ]);

        $articles = collect($data['response']['results'] ?? [])
            ->filter(fn ($article) => !empty($article['webUrl']))
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
            'title'        => $article['webTitle'] ?? 'Untitled Article',
            'description'  => Str::limit(
                strip_tags(data_get($article, 'fields.bodyText') ?? ''),
                150
            ),
            'content'      => data_get($article, 'fields.bodyText'),
            'author'       => collect($article['tags'] ?? [])
                ->firstWhere('type', 'contributor')['webTitle'] ?? 'Unknown',
            'source'       => $this->name(),
            'category'     => $article['pillarName'] ?? null,
            'url'          => $article['webUrl'],
            'image_url'    => data_get($article, 'fields.thumbnail'),
            'published_at' => isset($article['webPublicationDate'])
                ? Carbon::parse($article['webPublicationDate'])->toDateTimeString()
                : now()->toDateTimeString(),
        ];
    }
}
