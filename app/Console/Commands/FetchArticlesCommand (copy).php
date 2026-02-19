<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsAggregatorService;
use App\Services\NewsProviders\{
    NewsApiProvider,
    GNewsProvider,
    NewsDataProvider,
    TheGuardianProvider
};

class FetchArticlesCommand extends Command
{
    protected $signature = 'news:fetch';
    protected $description = 'Fetch news articles from APIs';

    public function handle(): int
    {
        $service = new NewsAggregatorService([
            new NewsApiProvider(),
            new GNewsProvider(),
            new NewsDataProvider(),
            new TheGuardianProvider()
        ]);

        $service->fetchAndStore();

        $this->info('Articles fetched successfully');

        return self::SUCCESS;
    }
}
