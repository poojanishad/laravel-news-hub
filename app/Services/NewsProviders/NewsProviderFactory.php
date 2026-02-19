<?php

namespace App\Services\NewsProviders;

use InvalidArgumentException;

class NewsProviderFactory
{
    public static function make(string $provider): array
    {
        return match ($provider) {
            'newsapi' => [new NewsApiProvider()],
            'gnews' => [new GNewsProvider()],
            'newsdata' => [new NewsDataProvider()],
            'thegaurdian' => [new TheGuardianProvider()],
            'all' => [
                new NewsApiProvider(),
                new GNewsProvider(),
                new NewsDataProvider(),
                new TheGuardianProvider(),
            ],
            default => throw new InvalidArgumentException("Invalid provider: {$provider}")
        };
    }
}
