<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class FiltersApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_by_single_source(): void
    {
        $src1 = Source::factory()->create(['name' => 'Alpha News', 'slug' => 'alpha-news']);
        $src2 = Source::factory()->create(['name' => 'Beta Times', 'slug' => 'beta-times']);

        Article::factory()->count(2)->create(['source_id' => $src1->id]);
        Article::factory()->create(['source_id' => $src2->id]);

        $res = $this->getJson('/api/articles?source=alpha-news');
        $res->assertStatus(200);
        $this->assertSame(2, $res->json('meta.total'));
    }

    public function test_filter_by_multiple_sources(): void
    {
        $src1 = Source::factory()->create(['slug' => 'one']);
        $src2 = Source::factory()->create(['slug' => 'two']);
        $src3 = Source::factory()->create(['slug' => 'three']);

        Article::factory()->create(['source_id' => $src1->id]);
        Article::factory()->create(['source_id' => $src2->id]);
        Article::factory()->create(['source_id' => $src3->id]);

        $res = $this->getJson('/api/articles?sources=one,two');
        $res->assertStatus(200);
        $this->assertSame(2, $res->json('meta.total'));
    }

    public function test_filter_by_category(): void
    {
        $src = Source::factory()->create();
        $tech = Category::factory()->create(['name' => 'Technology', 'slug' => 'technology']);
        $world = Category::factory()->create(['name' => 'World', 'slug' => 'world']);

        $a1 = Article::factory()->create(['source_id' => $src->id, 'url' => 'https://ex.com/tech']);
        $a2 = Article::factory()->create(['source_id' => $src->id, 'url' => 'https://ex.com/world']);
        $a1->categories()->attach($tech->id);
        $a2->categories()->attach($world->id);

        $res = $this->getJson('/api/articles?category=technology');
        $res->assertStatus(200);
        $this->assertSame(1, $res->json('meta.total'));
    }

    public function test_filter_by_author(): void
    {
        $src = Source::factory()->create();
        $john = Author::factory()->create(['name' => 'John Doe']);
        $jane = Author::factory()->create(['name' => 'Jane Doe']);
        $a1 = Article::factory()->create(['source_id' => $src->id, 'url' => 'https://ex.com/john']);
        $a2 = Article::factory()->create(['source_id' => $src->id, 'url' => 'https://ex.com/jane']);
        $a1->authors()->attach($john->id);
        $a2->authors()->attach($jane->id);

        $res = $this->getJson('/api/articles?author=John');
        $res->assertStatus(200);
        $this->assertSame(1, $res->json('meta.total'));
    }

    public function test_authors_endpoint_lists_authors(): void
    {
        Author::factory()->create(['name' => 'Alice X']);
        Author::factory()->create(['name' => 'Bob Y']);
        $res = $this->getJson('/api/authors');
        $res->assertStatus(200)
            ->assertJsonFragment(['name' => 'Alice X'])
            ->assertJsonFragment(['name' => 'Bob Y']);
    }
}
