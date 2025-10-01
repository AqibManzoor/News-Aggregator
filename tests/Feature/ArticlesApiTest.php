<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticlesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_get_articles_list()
    {
        // Create test data
        $source = Source::factory()->create();
        $category = Category::factory()->create();
        $author = Author::factory()->create();
        
        $article = Article::factory()->create([
            'source_id' => $source->id,
        ]);
        
        $article->categories()->attach($category);
        $article->authors()->attach($author);

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'slug',
                            'summary',
                            'url',
                            'image_url',
                            'published_at',
                            'source' => [
                                'id',
                                'name',
                                'slug'
                            ],
                            'categories' => [
                                '*' => [
                                    'id',
                                    'name',
                                    'slug'
                                ]
                            ],
                            'authors' => [
                                '*' => [
                                    'id',
                                    'name'
                                ]
                            ]
                        ]
                    ],
                    'meta' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total'
                    ]
                ]);
    }

    public function test_can_search_articles()
    {
        Article::factory()->create(['title' => 'Laravel News']);
        Article::factory()->create(['title' => 'PHP Updates']);

        $response = $this->getJson('/api/articles?q=Laravel');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals('Laravel News', $response->json('data.0.title'));
    }

    public function test_can_filter_by_source()
    {
        $source1 = Source::factory()->create(['name' => 'BBC News']);
        $source2 = Source::factory()->create(['name' => 'CNN']);
        
        Article::factory()->create(['source_id' => $source1->id]);
        Article::factory()->create(['source_id' => $source2->id]);

        $response = $this->getJson('/api/articles?source=BBC News');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_filter_by_category()
    {
        $category = Category::factory()->create(['name' => 'Technology']);
        $article = Article::factory()->create();
        $article->categories()->attach($category);

        $response = $this->getJson('/api/articles?category=Technology');

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_filter_by_date_range()
    {
        Article::factory()->create(['published_at' => now()->subDays(5)]);
        Article::factory()->create(['published_at' => now()->subDays(2)]);

        $response = $this->getJson('/api/articles?from=' . now()->subDays(3)->format('Y-m-d'));

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_sort_articles()
    {
        Article::factory()->create(['title' => 'Z Article', 'published_at' => now()->subDays(1)]);
        Article::factory()->create(['title' => 'A Article', 'published_at' => now()]);

        $response = $this->getJson('/api/articles?sort=title');

        $response->assertStatus(200);
        $this->assertEquals('A Article', $response->json('data.0.title'));
    }

    public function test_can_get_article_details()
    {
        $source = Source::factory()->create();
        $article = Article::factory()->create(['source_id' => $source->id]);

        $response = $this->getJson("/api/articles/{$article->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'id',
                        'title',
                        'slug',
                        'summary',
                        'url',
                        'source',
                        'categories',
                        'authors'
                    ]
                ]);
    }

    public function test_can_get_article_stats()
    {
        Article::factory()->count(5)->create();
        Source::factory()->count(3)->create();
        Category::factory()->count(2)->create();
        Author::factory()->count(4)->create();

        $response = $this->getJson('/api/articles/stats');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        'total_articles',
                        'articles_today',
                        'articles_this_week',
                        'sources_count',
                        'categories_count',
                        'authors_count'
                    ]
                ]);
    }

    public function test_validation_errors()
    {
        $response = $this->getJson('/api/articles?per_page=150');

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['per_page']);
    }
}