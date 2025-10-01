<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Services\CacheService;
use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    /**
     * Display a listing of authors.
     */
    public function index(): JsonResponse
    {
        // Try to get from cache first
        $cachedAuthors = $this->cacheService->getAuthors();
        if ($cachedAuthors) {
            return response()->json($cachedAuthors);
        }

        $authors = Author::query()
            ->withCount('articles')
            ->orderBy('name')
            ->get(['id', 'name']);

        $response = ['data' => $authors];
        
        // Cache the response
        $this->cacheService->putAuthors($response);

        return response()->json($response);
    }
}
