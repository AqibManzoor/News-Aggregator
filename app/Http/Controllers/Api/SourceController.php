<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Source;
use App\Services\CacheService;
use Illuminate\Http\JsonResponse;

class SourceController extends Controller
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    /**
     * Display a listing of sources.
     */
    public function index(): JsonResponse
    {
        // Try to get from cache first
        $cachedSources = $this->cacheService->getSources();
        if ($cachedSources) {
            return response()->json($cachedSources);
        }

        $sources = Source::query()
            ->withCount('articles')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'external_id', 'website_url']);

        $response = ['data' => $sources];
        
        // Cache the response
        $this->cacheService->putSources($response);

        return response()->json($response);
    }
}
