<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'summary' => $this->summary,
            'url' => $this->url,
            'image_url' => $this->image_url,
            'published_at' => optional($this->published_at)->toIso8601String(),
            'language' => $this->language,
            'source' => new SourceResource($this->whenLoaded('source')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'authors' => AuthorResource::collection($this->whenLoaded('authors')),
        ];
    }
}
