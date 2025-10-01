<?php

namespace App\Console\Commands;

use App\Services\AggregatorService;
use Illuminate\Console\Command;

class FetchNewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch 
                            {--q= : Search query}
                            {--category= : Category filter}
                            {--from= : From date (Y-m-d)}
                            {--to= : To date (Y-m-d)}
                            {--language=en : Language filter}
                            {--page=1 : Page number}
                            {--pageSize=50 : Number of articles per page}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and store news articles from configured providers';

    /**
     * Execute the console command.
     */
    public function handle(AggregatorService $aggregator): int
    {
        $this->info('Starting news aggregation...');

        $params = array_filter([
            'q' => $this->option('q'),
            'category' => $this->option('category'),
            'from' => $this->option('from'),
            'to' => $this->option('to'),
            'language' => $this->option('language'),
            'page' => (int) $this->option('page'),
            'pageSize' => (int) $this->option('pageSize'),
        ], fn($v) => $v !== null && $v !== '');

        try {
            $result = $aggregator->fetchAndStore($params);
            
            $this->info('News aggregation completed successfully!');
            $this->table(
                ['Metric', 'Count'],
                [
                    ['Fetched', $result['fetched']],
                    ['Inserted', $result['inserted']],
                    ['Updated', $result['updated']],
                ]
            );

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('News aggregation failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
