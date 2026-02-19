<?php

namespace App\Services\NewsProviders;

class NewsProviderFactory
{
    public function __construct(private array $providers) {}

    public function make(string $provider)
{
    return match ($provider) {
        'newsapi' => new NewsApiProvider(),
        'gnews' => new GNewsProvider(),
        'newsdata' => new NewsDataProvider(),
        'guardian' => new TheGuardianProvider(),
        default => throw new \Exception("Invalid provider: {$provider}")
    };
}

}
