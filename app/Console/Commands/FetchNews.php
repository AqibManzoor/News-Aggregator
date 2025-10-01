<?php

namespace App\Console\Commands;

use App\Services\AggregatorService;
use Illuminate\Console\Command;

class FetchNews extends Command
{
    protected $signature = 'news:fetch {--q=} {--category=} {--from=} {--to=} {--language=} {--pageSize=50}';
    protected $description = 'Fetch latest news from configured providers and store them locally';

    public function handle(AggregatorService $aggregator): int
    {
        $params = [
            'q' => $this->option('q'),
            'category' => $this->option('category'),
            'from' => $this->option('from'),
            'to' => $this->option('to'),
            'language' => $this->option('language'),
            'pageSize' => (int) $this->option('pageSize'),
        ];
        $params = array_filter($params, fn($v) => $v !== null && $v !== '');

        $result = $aggregator->fetchAndStore($params);
        $this->info("Fetched: {$result['fetched']}, Inserted: {$result['inserted']}, Updated: {$result['updated']}");
        return self::SUCCESS;
    }
}
