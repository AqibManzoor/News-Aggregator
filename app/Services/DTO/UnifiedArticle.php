<?php

namespace App\Services\DTO;

class UnifiedArticle
{
    public function __construct(
        public string $title,
        public ?string $summary,
        public ?string $content,
        public string $url,
        public ?string $imageUrl,
        public ?string $publishedAt,
        public ?string $language,
        public string $sourceName,
        public ?string $sourceExternalId,
        /** @var string[] */
        public array $categories = [],
        /** @var string[] */
        public array $authors = [],
        public ?string $articleExternalId = null,
    ) {
    }

    /**
     * Convert to array for persistence.
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'summary' => $this->summary,
            'content' => $this->content,
            'url' => $this->url,
            'image_url' => $this->imageUrl,
            'published_at' => $this->publishedAt,
            'language' => $this->language,
            'source_name' => $this->sourceName,
            'source_external_id' => $this->sourceExternalId,
            'categories' => $this->categories,
            'authors' => $this->authors,
            'external_id' => $this->articleExternalId,
        ];
    }
}
