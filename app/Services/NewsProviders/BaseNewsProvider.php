<?php

namespace App\Services\NewsProviders;

use App\Contracts\NewsProviderInterface;
use Illuminate\Support\Facades\Http;

abstract class BaseNewsProvider implements NewsProviderInterface
{
    protected function get(string $url, array $params): array
    {
        $response = Http::timeout(10)
            ->retry(3, 200)
            ->get($url, $params);

        if ($response->failed()) {
            throw new \RuntimeException(
                static::class . ' failed with status ' . $response->status()
            );
        }

        return $response->json();
    }
}
