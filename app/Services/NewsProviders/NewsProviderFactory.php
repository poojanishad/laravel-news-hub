<?php

namespace App\Services\NewsProviders;

use InvalidArgumentException;
class NewsProviderFactory
{
    public function __construct(private array $providers) {}

    public function make(string $provider): array
    {
        if ($provider === 'all') {
            return array_values($this->providers);
        }

        if (! isset($this->providers[$provider])) {
            throw new InvalidArgumentException("Invalid provider: {$provider}");
        }

        return [$this->providers[$provider]];
    }
}
