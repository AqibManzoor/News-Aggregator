<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleIndexRequest;
use App\Models\Article;
use App\Http\Resources\ArticleResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct() {}

    /**
     * Display a listing of articles with filtering and search capabilities.
     */
    public function index(ArticleIndexRequest $request): JsonResponse
    {
        $filters = $request->validated();
        $page = $request->input('page', 1);
        $perPage = $request->validated('per_page', 20);

        // No caching in API layer; compute fresh response

        $query = Article::query()->with(['source:id,name,slug', 'categories:id,name,slug', 'authors:id,name']);

        // Search functionality
        if ($search = $request->validated('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('summary', 'like', "%$search%")
                  ->orWhere('content', 'like', "%$search%");
            });
        }

        // Source filtering
        if ($source = $request->validated('source')) {
            $query->whereHas('source', fn($q) => $q->where('slug', $source)->orWhere('name', 'like', "%$source%"));
        }
        
        $sources = $request->validated('sources');
        if ($sources) {
            $query->whereHas('source', function ($q) use ($sources) {
                $q->whereIn('slug', $sources)->orWhereIn('name', $sources);
            });
        }

        // Category filtering
        if ($category = $request->validated('category')) {
            $query->whereHas('categories', fn($q) => $q->where('slug', $category)->orWhere('name', 'like', "%$category%"));
        }
        
        $categories = $request->validated('categories');
        if ($categories) {
            $query->whereHas('categories', function ($q) use ($categories) {
                $q->whereIn('slug', $categories)->orWhereIn('name', $categories);
            });
        }

        // Author filtering
        if ($author = $request->validated('author')) {
            $query->whereHas('authors', fn($q) => $q->where('name', 'like', "%$author%"));
        }
        
        $authors = $request->validated('authors');
        if ($authors) {
            $query->whereHas('authors', function ($q) use ($authors) {
                $q->whereIn('name', $authors);
            });
        }

        // Date filtering
        if ($from = $request->validated('from')) {
            $query->where('published_at', '>=', $from);
        }
        if ($to = $request->validated('to')) {
            $query->where('published_at', '<=', $to);
        }

        // Sorting
        $sort = $request->validated('sort', 'newest');
        switch ($sort) {
            case 'oldest':
                $query->orderBy('published_at', 'asc');
                break;
            case 'title':
                $query->orderBy('title');
                break;
            default:
                $query->orderByDesc('published_at');
        }

        // Pagination
        $articles = $query->paginate($perPage);

        return response()->json([
            'data' => ArticleResource::collection($articles->items()),
            'meta' => [
                'current_page' => $articles->currentPage(),
                'last_page' => $articles->lastPage(),
                'per_page' => $articles->perPage(),
                'total' => $articles->total(),
                'from' => $articles->firstItem(),
                'to' => $articles->lastItem(),
            ],
            'links' => [
                'first' => $articles->url(1),
                'last' => $articles->url($articles->lastPage()),
                'prev' => $articles->previousPageUrl(),
                'next' => $articles->nextPageUrl(),
            ],
        ]);
    }

    /**
     * Display the specified article.
     */
    public function show(Article $article): JsonResponse
    {
        $article->load(['source:id,name,slug,website_url', 'categories:id,name,slug', 'authors:id,name']);
        return response()->json(['data' => new ArticleResource($article)]);
    }

    /**
     * Return aggregate stats for articles and related entities.
     */
    public function stats(): JsonResponse
    {
        $today = now()->startOfDay();
        $weekStart = now()->startOfWeek();

        return response()->json([
            'data' => [
                'total_articles' => Article::count(),
                'articles_today' => Article::whereDate('published_at', '>=', $today)->count(),
                'articles_this_week' => Article::whereDate('published_at', '>=', $weekStart)->count(),
                'sources_count' => \App\Models\Source::count(),
                'categories_count' => \App\Models\Category::count(),
                'authors_count' => \App\Models\Author::count(),
            ],
        ]);
    }


}
