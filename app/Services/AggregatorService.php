<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AggregatorService
{
    /**
     * Fetch from enabled providers and store/upsert articles.
     *
     * @param array $params
     * @return array{fetched:int,inserted:int,updated:int}
     */
    public function fetchAndStore(array $params = []): array
    {
        $fetched = 0; $inserted = 0; $updated = 0;

        /** @var array<int,object> $providers */
        try {
            $providers = App::make('news.providers');
        } catch (\Throwable $e) {
            Log::error('Failed to load news providers', ['error' => $e->getMessage()]);
            $providers = [];
        }
        foreach ($providers as $provider) {
            if (!method_exists($provider, 'fetch')) { continue; }
            
            try {
                $items = $provider->fetch($params);
                $fetched += $items->count();
            } catch (\Throwable $e) {
                Log::error('Provider fetch failed', [
                    'provider' => get_class($provider),
                    'error' => $e->getMessage()
                ]);
                continue;
            }

            foreach ($items as $item) {
                $title = (string) ($item['title'] ?? '');
                $url = (string) ($item['url'] ?? '');
                if ($title === '' || $url === '') {
                    continue;
                }

                DB::beginTransaction();
                try {
                    $source = Source::firstOrCreate(
                        [
                            'slug' => Str::slug((string) ($item['source_external_id'] ?? $item['source_name'] ?? 'unknown')),
                        ],
                        [
                            'name' => (string) ($item['source_name'] ?? 'Unknown'),
                            'external_id' => $item['source_external_id'] ?? null,
                            'website_url' => null,
                        ]
                    );

                    $slug = Str::slug(Str::limit($title, 100, ''));

                    $article = Article::where('url', $url)
                        ->orWhere(fn($q) => !empty($item['external_id']) ? $q->where('external_id', $item['external_id']) : $q)
                        ->first();

                    $data = [
                        'source_id' => $source->id,
                        'title' => $title,
                        'slug' => $slug,
                        'summary' => $item['summary'] ?? null,
                        'content' => $item['content'] ?? null,
                        'url' => $url,
                        'image_url' => $item['image_url'] ?? null,
                        'published_at' => $item['published_at'] ?? null,
                        'language' => $item['language'] ?? config('news.defaults.language'),
                        'external_id' => $item['external_id'] ?? null,
                    ];

                    if ($article) {
                        $article->fill($data);
                        $article->save();
                        $updated++;
                    } else {
                        $article = Article::create($data);
                        $inserted++;
                    }

                    $catIds = collect($item['categories'] ?? [])
                        ->filter()
                        ->map(fn($c) => Str::lower(trim((string) $c)))
                        ->unique()
                        ->map(function ($c) {
                            return Category::firstOrCreate(
                                ['slug' => Str::slug($c)],
                                ['name' => Str::title($c)]
                            )->id;
                        })->values()->all();
                    if (!empty($catIds)) {
                        $article->categories()->syncWithoutDetaching($catIds);
                    }

                    $authorIds = collect($item['authors'] ?? [])
                        ->filter()
                        ->map(fn($a) => trim((string) $a))
                        ->unique()
                        ->map(fn($a) => Author::firstOrCreate(['name' => $a])->id)
                        ->values()->all();
                    if (!empty($authorIds)) {
                        $article->authors()->syncWithoutDetaching($authorIds);
                    }

                    DB::commit();
                } catch (\Throwable $e) {
                    DB::rollBack();
                    Log::warning('Aggregator failed to upsert item', [
                        'provider' => method_exists($provider, 'key') ? $provider->key() : get_class($provider),
                        'url' => $url,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        return compact('fetched', 'inserted', 'updated');
    }
}
