<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_id',
        'title',
        'slug',
        'summary',
        'content',
        'url',
        'image_url',
        'published_at',
        'language',
        'external_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function source(): BelongsTo
    {
        return $this->belongsTo(Source::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'article_category')->withTimestamps();
    }

    public function authors(): BelongsToMany
    {
        return $this->belongsToMany(Author::class, 'article_author')->withTimestamps();
    }
}
