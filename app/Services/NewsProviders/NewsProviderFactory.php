<?php

namespace App\Services\NewsProviders;

use App\Contracts\NewsProviderInterface;
use InvalidArgumentException;

class NewsProviderFactory
{
    public function make(string $provider): NewsProviderInterface
    {
        return match ($provider) {
            'gnews'    => new GNewsProvider(),
            'newsapi'  => new NewsApiProvider(),
            'newsdata' => new NewsDataProvider(),
            'guardian' => new TheGuardianProvider(),

            default => throw new InvalidArgumentException(
                "Invalid provider {$provider}"
            ),
        };
    }
}
