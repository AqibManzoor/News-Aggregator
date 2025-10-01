<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CacheService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    /**
     * Display a listing of categories.
     */
    public function index(): JsonResponse
    {
        // Try to get from cache first
        $cachedCategories = $this->cacheService->getCategories();
        if ($cachedCategories) {
            return response()->json($cachedCategories);
        }

        $categories = Category::query()
            ->withCount('articles')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $response = ['data' => $categories];
        
        // Cache the response
        $this->cacheService->putCategories($response);

        return response()->json($response);
    }
}
