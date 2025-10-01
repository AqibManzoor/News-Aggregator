<?php

namespace App\Services\Providers;

use App\Services\Contracts\NewsProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class NewsApiProvider implements NewsProvider
{
    public function key(): string { return 'newsapi'; }

    public function fetch(array $params = []): Collection
    {
        $apiKey = config('services.newsapi.key');
        if (!$apiKey) {
            return collect();
        }

        // Use everything endpoint for broader coverage
        $baseUrl = 'https://newsapi.org/v2/everything';
        $query = array_filter([
            'q' => $params['q'] ?? '*', // Default to all articles if no query
            'from' => $params['from'] ?? null,
            'to' => $params['to'] ?? null,
            'language' => $params['language'] ?? 'en',
            'sortBy' => 'publishedAt',
            'page' => $params['page'] ?? 1,
            'pageSize' => min($params['pageSize'] ?? 50, 100), // NewsAPI max is 100
            'apiKey' => $apiKey,
        ], fn($v) => $v !== null);

        $resp = Http::retry(2, 200)->get($baseUrl, $query);
        if (!$resp->ok()) {
            \Log::warning('NewsAPI request failed', [
                'status' => $resp->status(),
                'body' => $resp->body()
            ]);
            return collect();
        }

        $json = $resp->json();
        $articles = $json['articles'] ?? [];

        return collect($articles)->map(function ($a) {
            $source = $a['source'] ?? [];
            return [
                'title' => $a['title'] ?? '',
                'summary' => $a['description'] ?? null,
                'content' => $a['content'] ?? null,
                'url' => $a['url'] ?? '',
                'image_url' => $a['urlToImage'] ?? null,
                'published_at' => $a['publishedAt'] ?? null,
                'language' => $a['language'] ?? 'en',
                'source_name' => $source['name'] ?? 'NewsAPI',
                'source_external_id' => $source['id'] ?? null,
                'categories' => array_filter([(string) ($a['category'] ?? 'general')]),
                'authors' => !empty($a['author']) ? [trim((string) $a['author'])] : [],
                'external_id' => $a['url'] ?? null,
            ];
        });
    }
}
