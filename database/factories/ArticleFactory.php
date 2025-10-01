<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Article> */
class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(6);
        return [
            'source_id' => Source::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'summary' => $this->faker->sentence(12),
            'content' => $this->faker->paragraphs(3, true),
            'url' => $this->faker->unique()->url(),
            'image_url' => $this->faker->optional()->imageUrl(),
            'published_at' => now()->subDays(rand(0, 10)),
            'language' => 'en',
        ];
    }
}
 
