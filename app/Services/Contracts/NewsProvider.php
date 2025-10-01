<?php

namespace App\Services\Contracts;

use Illuminate\Support\Collection;

interface NewsProvider
{
    /**
     * Fetch latest articles, optionally filtered by keyword or category.
     * Should return a collection of UnifiedArticle DTO arrays.
     *
     * @param array{q?:string,category?:string,from?:string,to?:string,language?:string,page?:int,pageSize?:int} $params
     * @return Collection<int,array>
     */
    public function fetch(array $params = []): Collection;

    /** Provider machine name, e.g., 'newsapi', 'guardian', 'nyt' */
    public function key(): string;
}
