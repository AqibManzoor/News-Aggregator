<?php

namespace Tests\Unit;

use App\Services\Providers\GuardianProvider;
use App\Services\Providers\NewsApiProvider;
use App\Services\Providers\NytProvider;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProvidersTest extends TestCase
{
    public function test_newsapi_mapping(): void
    {
        Http::fake([
            'newsapi.org/*' => Http::response([
                'articles' => [[
                    'title' => 'A', 'description' => 'D', 'content' => 'C',
                    'url' => 'https://x/a', 'urlToImage' => 'https://img/a.jpg', 'publishedAt' => '2023-01-01T00:00:00Z',
                    'source' => ['name' => 'NewsAPI', 'id' => 'newsapi']
                ]]
            ], 200)
        ]);
        config(['services.newsapi.key' => 'k']);
        $prov = new NewsApiProvider();
        $items = $prov->fetch();
        $this->assertCount(1, $items);
        $this->assertSame('A', $items[0]['title']);
    }

    public function test_guardian_mapping(): void
    {
        Http::fake([
            'content.guardianapis.com/*' => Http::response([
                'response' => ['results' => [[
                    'webTitle' => 'G', 'webUrl' => 'https://g/1', 'webPublicationDate' => '2023-01-01T00:00:00Z',
                    'sectionName' => 'World', 'id' => 'g-1', 'fields' => ['trailText' => 't', 'bodyText' => 'b']
                ]]]
            ], 200)
        ]);
        config(['services.guardian.key' => 'k']);
        $prov = new GuardianProvider();
        $items = $prov->fetch();
        $this->assertCount(1, $items);
        $this->assertSame('G', $items[0]['title']);
    }

    public function test_nyt_mapping(): void
    {
        Http::fake([
            'api.nytimes.com/*' => Http::response([
                'results' => [[
                    'title' => 'N', 'abstract' => 'a', 'url' => 'https://n/1', 'published_date' => '2023-01-01T00:00:00Z',
                    'section' => 'Tech', 'uri' => 'nyt://123', 'byline' => 'By John', 'multimedia' => [['url' => 'u']]
                ]]
            ], 200)
        ]);
        config(['services.nyt.key' => 'k']);
        $prov = new NytProvider();
        $items = $prov->fetch();
        $this->assertCount(1, $items);
        $this->assertSame('N', $items[0]['title']);
    }
}
