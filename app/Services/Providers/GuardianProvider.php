<?php

namespace App\Services\Providers;

use App\Services\Contracts\NewsProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

class GuardianProvider implements NewsProvider
{
    public function key(): string { return 'guardian'; }

    public function fetch(array $params = []): Collection
    {
        $apiKey = config('services.guardian.key');
        if (!$apiKey) { return collect(); }

        $url = 'https://content.guardianapis.com/search';
        $query = array_filter([
            'q' => $params['q'] ?? null,
            'from-date' => $params['from'] ?? null,
            'to-date' => $params['to'] ?? null,
            'page' => $params['page'] ?? 1,
            'page-size' => $params['pageSize'] ?? 50,
            'show-fields' => 'trailText,bodyText,thumbnail',
            'api-key' => $apiKey,
        ], fn($v) => $v !== null);

        $resp = Http::retry(2, 200)->get($url, $query);
        if (!$resp->ok()) { return collect(); }

        $json = $resp->json();
        $results = $json['response']['results'] ?? [];

        return collect($results)->map(function ($r) {
            $fields = $r['fields'] ?? [];
            return [
                'title' => $r['webTitle'] ?? '',
                'summary' => $fields['trailText'] ?? null,
                'content' => $fields['bodyText'] ?? null,
                'url' => $r['webUrl'] ?? '',
                'image_url' => $fields['thumbnail'] ?? null,
                'published_at' => $r['webPublicationDate'] ?? null,
                'language' => null,
                'source_name' => 'The Guardian',
                'source_external_id' => 'guardian',
                'categories' => array_filter([(string) ($r['sectionName'] ?? '')]),
                'authors' => [],
                'external_id' => $r['id'] ?? null,
            ];
        });
    }
}
