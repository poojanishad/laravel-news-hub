<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsAggregatorService;
use App\Services\NewsProviders\NewsProviderFactory;

class FetchArticlesCommand extends Command
{
    protected $signature = 'news:fetch {--provider=all}';
    protected $description = 'Fetch news articles from APIs';

    public function handle(NewsProviderFactory $factory): int
    {
        try {

            $providerOption = $this->option('provider');

            $service = new NewsAggregatorService($factory);

            if ($providerOption === 'all') {
                $service->fetchFromAll();
            } else {
                $service->fetchFromSingle($providerOption);
            }

            $this->info('Articles fetched successfully');

            return self::SUCCESS;

        } catch (\Throwable $e) {

            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
