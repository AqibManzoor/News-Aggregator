<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SourceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'website_url' => $this->website_url,
        ];
    }
}
