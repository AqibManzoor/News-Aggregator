<?php

namespace App\Services\Providers;

use App\Services\Contracts\NewsProvider;
use App\Services\DTO\UnifiedArticle;
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
            
            // Create UnifiedArticle DTO for standardized data structure
            $unifiedArticle = new UnifiedArticle(
                title: $r['webTitle'] ?? '',
                summary: $fields['trailText'] ?? null,
                content: $fields['bodyText'] ?? null,
                url: $r['webUrl'] ?? '',
                imageUrl: $fields['thumbnail'] ?? null,
                publishedAt: $r['webPublicationDate'] ?? null,
                language: 'en', // Guardian is primarily English
                sourceName: 'The Guardian',
                sourceExternalId: 'guardian',
                categories: array_filter([(string) ($r['sectionName'] ?? '')]),
                authors: [], // Guardian API doesn't provide author info in basic search
                articleExternalId: $r['id'] ?? null
            );
            
            return $unifiedArticle->toArray();
        });
    }
}
