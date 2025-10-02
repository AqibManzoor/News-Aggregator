<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Http\Resources\CategoryResource;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct() {}

    /**
     * Display a listing of categories.
     */
    public function index(): JsonResponse
    {
        $categories = Category::query()
            ->withCount('articles')
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);
        return response()->json(['data' => CategoryResource::collection($categories)]);
    }
}
