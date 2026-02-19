<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsAggregatorService;
use App\Services\NewsProviders\NewsProviderFactory;
use App\Services\NewsProviders\{
    NewsApiProvider,
    GNewsProvider,
    NewsDataProvider,
    TheGuardianProvider
};

class FetchArticlesCommand extends Command
{
    protected $signature = 'news:fetch {--provider=all}';
    protected $description = 'Fetch news articles from APIs';

    public function handle147(): int
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

    public function handle(): int
    {
        $providerOption = $this->option('provider');

        try {
            $providers = NewsProviderFactory::make($providerOption);

            $service = new NewsAggregatorService($providers);
            $service->fetchAndStore();

            $this->info("Articles fetched successfully for: {$providerOption}");
            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
