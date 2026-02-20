<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\NewsAggregatorService;

class FetchArticlesCommand extends Command
{
    protected $signature = 'news:fetch {--provider=all}';
    protected $description = 'Fetch news articles from APIs';

    public function handle(NewsAggregatorService $service): int
    {
        try {
            $provider = $this->option('provider');

            if ($provider === 'all') {
                $service->fetchFromAll();
            } else {
                $service->fetchFromSingle($provider);
            }

            $this->info('Articles fetched successfully');

            return self::SUCCESS;

        } catch (\Throwable $e) {

            $this->error($e->getMessage());

            return self::FAILURE;
        }
    }
}