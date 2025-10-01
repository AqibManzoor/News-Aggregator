<?php

namespace Database\Factories;

use App\Models\Source;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Source> */
class SourceFactory extends Factory
{
    protected $model = Source::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company();
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'website_url' => $this->faker->url(),
        ];
    }
}
 
