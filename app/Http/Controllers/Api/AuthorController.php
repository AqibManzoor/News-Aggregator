<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Author;
use App\Http\Resources\AuthorResource;
use Illuminate\Http\JsonResponse;

class AuthorController extends Controller
{
    public function __construct() {}

    /**
     * Display a listing of authors.
     */
    public function index(): JsonResponse
    {
        $authors = Author::query()
            ->withCount('articles')
            ->orderBy('name')
            ->get(['id', 'name']);
        return response()->json(['data' => AuthorResource::collection($authors)]);
    }
}
