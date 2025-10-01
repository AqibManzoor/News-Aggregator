<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Source extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'external_id',
        'website_url',
        'logo_url',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'article_category')->withTimestamps();
    }
}
