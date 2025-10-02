<?php

namespace App\Services\Providers;

use App\Services\Contracts\NewsProvider;
use App\Services\DTO\UnifiedArticle;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class NytProvider implements NewsProvider
{
    public function key(): string { return 'nyt'; }

    public function fetch(array $params = []): Collection
    {
        $apiKey = config('services.nyt.key');
        if (!$apiKey) { return collect(); }

        $url = 'https://api.nytimes.com/svc/topstories/v2/home.json';
        $query = ['api-key' => $apiKey];
        $resp = Http::retry(2, 200)->get($url, $query);
        if (!$resp->ok()) { return collect(); }

        $json = $resp->json();
        $results = $json['results'] ?? [];

        return collect($results)->map(function ($r) {
            $image = null;
            if (!empty($r['multimedia']) && is_array($r['multimedia'])) {
                $image = $r['multimedia'][0]['url'] ?? null;
            }
            
            $authors = [];
            if (!empty($r['byline'])) {
                $authors = [trim(preg_replace('/^By\s+/i', '', $r['byline']))];
            }
            
            // Create UnifiedArticle DTO for standardized data structure
            $unifiedArticle = new UnifiedArticle(
                title: $r['title'] ?? '',
                summary: $r['abstract'] ?? null,
                content: null, // NYT API doesn't provide full content in basic fetch
                url: $r['url'] ?? '',
                imageUrl: $image,
                publishedAt: $r['published_date'] ?? null,
                language: 'en', // NYT is primarily English
                sourceName: 'The New York Times',
                sourceExternalId: 'nyt',
                categories: array_filter([(string) ($r['section'] ?? '')]),
                authors: $authors,
                articleExternalId: $r['uri'] ?? $r['url'] ?? null
            );
            
            return $unifiedArticle->toArray();
        });
    }
}
