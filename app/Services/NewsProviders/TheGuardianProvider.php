<?php

namespace App\Services\NewsProviders;

use App\Contracts\NewsProviderInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TheGuardianProvider implements NewsProviderInterface
{
    public function fetch(): array
    {
        Log::info('Fetching from The Guardian');
        $response = Http::get('https://content.guardianapis.com/search', [
            'from-date'   => '2026-01-01',
            'page-size'   =>  config('services.general.pageSize'),
            'show-fields' => 'thumbnail,bodyText',
            'show-tags'   => 'contributor',
            'api-key'     => config('services.guardiankey.key'),
        ]);

        if ($response->failed()) {
            Log::error('TheGuardianProvider request failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [];
        }

        $articles = collect($response->json('response.results', []))
            ->map(fn($a) => [
                'title'        => $a['webTitle'] ?? null,
                'description' => Str::limit(
                    strip_tags(
                        data_get($a, 'fields.bodyText')
                        ?? data_get($a, 'webTitle')
                        ?? ''
                    ),
                    150
                ),
                'content'      => $a['fields']['bodyText'] ?? null,
                'author'       => $a['tags'][0]['webTitle'] ?? null,
                'source'       => 'TheGuardian',
                'category'     => $a['pillarName'] ?? null,
                'url'          => $a['webUrl'] ?? null,
                'image_url'    => $a['fields']['thumbnail'] ?? null,
                'published_at' => $a['webPublicationDate'] ?? null,
            ])
            ->filter(fn($a) => ! empty($a['url']))
            ->values()
            ->toArray();

        Log::info('TheGuardianProvider fetched articles', [
            'count' => count($articles),
        ]);
        return $articles;
    }
}
