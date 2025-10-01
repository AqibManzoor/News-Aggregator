<?php

namespace App\Services\Providers;

use App\Services\Contracts\NewsProvider;
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
            return [
                'title' => $r['title'] ?? '',
                'summary' => $r['abstract'] ?? null,
                'content' => null,
                'url' => $r['url'] ?? '',
                'image_url' => $image,
                'published_at' => $r['published_date'] ?? null,
                'language' => null,
                'source_name' => 'The New York Times',
                'source_external_id' => 'nyt',
                'categories' => array_filter([(string) ($r['section'] ?? '')]),
                'authors' => $authors,
                'external_id' => $r['uri'] ?? $r['url'] ?? null,
            ];
        });
    }
}
