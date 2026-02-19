<?php

namespace App\Contracts;

interface NewsProviderInterface
{
    /**
     * Fetch articles from external API
     *
     * @return array
     */
    public function fetch(): array;
    public function name(): string;
}
