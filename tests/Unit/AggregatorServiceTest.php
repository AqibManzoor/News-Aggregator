<?php

namespace Tests\Unit;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Services\AggregatorService;
use App\Services\Providers\NewsApiProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class AggregatorServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_fetch_and_store_articles()
    {
        // Mock the news providers
        $mockProvider = $this->createMock(NewsApiProvider::class);
        $mockProvider->method('key')->willReturn('newsapi');
        $mockProvider->method('fetch')->willReturn(collect([
            [
                'title' => 'Test Article',
                'summary' => 'Test summary',
                'url' => 'https://example.com/article',
                'published_at' => now()->toISOString(),
                'source_name' => 'Test Source',
                'source_external_id' => 'test-source',
                'categories' => ['Technology'],
                'authors' => ['John Doe'],
                'external_id' => 'test-123',
            ]
        ]));

        // Bind the mock provider
        $this->app->instance('news.providers', [$mockProvider]);

        $service = new AggregatorService();
        $result = $service->fetchAndStore();

        $this->assertEquals(1, $result['fetched']);
        $this->assertEquals(1, $result['inserted']);
        $this->assertEquals(0, $result['updated']);

        // Verify article was created
        $this->assertDatabaseHas('articles', [
            'title' => 'Test Article',
            'url' => 'https://example.com/article',
        ]);

        // Verify source was created
        $this->assertDatabaseHas('sources', [
            'name' => 'Test Source',
            'external_id' => 'test-source',
        ]);

        // Verify category was created
        $this->assertDatabaseHas('categories', [
            'name' => 'Technology',
        ]);

        // Verify author was created
        $this->assertDatabaseHas('authors', [
            'name' => 'John Doe',
        ]);
    }

    public function test_updates_existing_article()
    {
        // Create existing article
        $source = Source::factory()->create();
        $article = Article::factory()->create([
            'source_id' => $source->id,
            'url' => 'https://example.com/article',
            'title' => 'Old Title',
        ]);

        // Mock provider with updated data
        $mockProvider = $this->createMock(NewsApiProvider::class);
        $mockProvider->method('key')->willReturn('newsapi');
        $mockProvider->method('fetch')->willReturn(collect([
            [
                'title' => 'Updated Title',
                'summary' => 'Updated summary',
                'url' => 'https://example.com/article',
                'published_at' => now()->toISOString(),
                'source_name' => $source->name,
                'source_external_id' => $source->external_id,
                'categories' => [],
                'authors' => [],
                'external_id' => 'test-123',
            ]
        ]));

        $this->app->instance('news.providers', [$mockProvider]);

        $service = new AggregatorService();
        $result = $service->fetchAndStore();

        $this->assertEquals(1, $result['fetched']);
        $this->assertEquals(0, $result['inserted']);
        $this->assertEquals(1, $result['updated']);

        // Verify article was updated
        $article->refresh();
        $this->assertEquals('Updated Title', $article->title);
    }

    public function test_handles_provider_errors_gracefully()
    {
        // Mock provider that throws exception
        $mockProvider = $this->createMock(NewsApiProvider::class);
        $mockProvider->method('key')->willReturn('newsapi');
        $mockProvider->method('fetch')->willThrowException(new \Exception('API Error'));

        $this->app->instance('news.providers', [$mockProvider]);

        $service = new AggregatorService();
        $result = $service->fetchAndStore();

        $this->assertEquals(0, $result['fetched']);
        $this->assertEquals(0, $result['inserted']);
        $this->assertEquals(0, $result['updated']);
    }

    public function test_skips_invalid_articles()
    {
        // Mock provider with invalid data
        $mockProvider = $this->createMock(NewsApiProvider::class);
        $mockProvider->method('key')->willReturn('newsapi');
        $mockProvider->method('fetch')->willReturn(collect([
            [
                'title' => '', // Empty title
                'url' => 'https://example.com/article',
            ],
            [
                'title' => 'Valid Article',
                'url' => '', // Empty URL
            ],
            [
                'title' => 'Another Valid Article',
                'url' => 'https://example.com/valid',
                'published_at' => now()->toISOString(),
                'source_name' => 'Test Source',
                'source_external_id' => 'test-source',
                'categories' => [],
                'authors' => [],
                'external_id' => 'test-456',
            ]
        ]));

        $this->app->instance('news.providers', [$mockProvider]);

        $service = new AggregatorService();
        $result = $service->fetchAndStore();

        $this->assertEquals(3, $result['fetched']);
        $this->assertEquals(1, $result['inserted']);
        $this->assertEquals(0, $result['updated']);
    }
}
