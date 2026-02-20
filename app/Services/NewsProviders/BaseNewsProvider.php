<?php

namespace App\Services\NewsProviders;

use Illuminate\Support\Facades\Http;
use App\Contracts\NewsProviderInterface;

abstract class BaseNewsProvider implements NewsProviderInterface
{
    protected function get(string $url, array $params = []): array
    {
        $response = Http::timeout(10)
            ->retry(3, 200)
            ->get($url, $params);

        if (!$response->successful()) {
            throw new \RuntimeException(
                static::class . ' failed with status ' . $response->status()
            );
        }

        return $response->json();
    }
}