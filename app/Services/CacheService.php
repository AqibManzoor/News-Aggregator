<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class CacheService
{
    /**
     * Cache key prefixes
     */
    const ARTICLES_KEY = 'articles';
    const SOURCES_KEY = 'sources';
    const CATEGORIES_KEY = 'categories';
    const AUTHORS_KEY = 'authors';
    const STATS_KEY = 'stats';

    /**
     * Cache duration in minutes
     */
    const DEFAULT_TTL = 60; // 1 hour
    const STATS_TTL = 30; // 30 minutes

    /**
     * Get cached articles with filters
     */
    public function getArticles(array $filters = [], int $page = 1, int $perPage = 20): ?array
    {
        $key = $this->buildKey(self::ARTICLES_KEY, $filters, $page, $perPage);
        return Cache::get($key);
    }

    /**
     * Cache articles with filters
     */
    public function putArticles(array $filters, int $page, int $perPage, array $data, int $ttl = self::DEFAULT_TTL): void
    {
        $key = $this->buildKey(self::ARTICLES_KEY, $filters, $page, $perPage);
        Cache::put($key, $data, $ttl);
    }

    /**
     * Get cached sources
     */
    public function getSources(): ?array
    {
        return Cache::get(self::SOURCES_KEY);
    }

    /**
     * Cache sources
     */
    public function putSources(array $data, int $ttl = self::DEFAULT_TTL): void
    {
        Cache::put(self::SOURCES_KEY, $data, $ttl);
    }

    /**
     * Get cached categories
     */
    public function getCategories(): ?array
    {
        return Cache::get(self::CATEGORIES_KEY);
    }

    /**
     * Cache categories
     */
    public function putCategories(array $data, int $ttl = self::DEFAULT_TTL): void
    {
        Cache::put(self::CATEGORIES_KEY, $data, $ttl);
    }

    /**
     * Get cached authors
     */
    public function getAuthors(): ?array
    {
        return Cache::get(self::AUTHORS_KEY);
    }

    /**
     * Cache authors
     */
    public function putAuthors(array $data, int $ttl = self::DEFAULT_TTL): void
    {
        Cache::put(self::AUTHORS_KEY, $data, $ttl);
    }

    /**
     * Get cached stats
     */
    public function getStats(): ?array
    {
        return Cache::get(self::STATS_KEY);
    }

    /**
     * Cache stats
     */
    public function putStats(array $data, int $ttl = self::STATS_TTL): void
    {
        Cache::put(self::STATS_KEY, $data, $ttl);
    }

    /**
     * Clear all cache
     */
    public function clearAll(): void
    {
        Cache::flush();
    }

    /**
     * Clear specific cache type
     */
    public function clearType(string $type): void
    {
        $patterns = [
            self::ARTICLES_KEY => 'articles:*',
            self::SOURCES_KEY => 'sources',
            self::CATEGORIES_KEY => 'categories',
            self::AUTHORS_KEY => 'authors',
            self::STATS_KEY => 'stats',
        ];

        if (isset($patterns[$type])) {
            if ($type === self::ARTICLES_KEY) {
                // For articles, we need to clear all possible filter combinations
                // This is a simplified approach - in production, you might want to use tags
                Cache::forget($patterns[$type]);
            } else {
                Cache::forget($patterns[$type]);
            }
        }
    }

    /**
     * Build cache key from filters
     */
    private function buildKey(string $prefix, array $filters, int $page, int $perPage): string
    {
        $filterString = md5(serialize($filters));
        return "{$prefix}:{$filterString}:{$page}:{$perPage}";
    }
}
