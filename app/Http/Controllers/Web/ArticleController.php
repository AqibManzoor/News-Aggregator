<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Services\AggregatorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        // Handle both GET and POST requests. For POST, we store filters in session
        $filters = $this->getFilters($request);
        
        $query = Article::query()->with(['source:id,name,slug', 'categories:id,name,slug', 'authors:id,name']);

        if (!empty($filters['q'])) {
            $search = $filters['q'];
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%$search%")
                  ->orWhere('summary', 'like', "%$search%")
                  ->orWhere('content', 'like', "%$search%");
            });
        }

        if (!empty($filters['sources'])) {
            $sources = is_array($filters['sources']) ? $filters['sources'] : [$filters['sources']];
            $sources = array_filter(array_map('trim', $sources));
            if (!empty($sources)) {
                $query->whereHas('source', function ($q) use ($sources) {
                    $q->whereIn('slug', $sources);
                });
            }
        }

        if (!empty($filters['categories'])) {
            $categories = is_array($filters['categories']) ? $filters['categories'] : [$filters['categories']];
            $categories = array_filter(array_map('trim', $categories));
            if (!empty($categories)) {
                $query->whereHas('categories', function ($q) use ($categories) {
                    $q->whereIn('slug', $categories);
                });
            }
        }

        if (!empty($filters['authors'])) {
            $authors = is_array($filters['authors']) ? $filters['authors'] : [$filters['authors']];
            $authors = array_filter(array_map('trim', $authors));
            if (!empty($authors)) {
                $query->whereHas('authors', function ($q) use ($authors) {
                    $q->whereIn('name', $authors);
                });
            }
        }

        if (!empty($filters['from'])) {
            // dd($filters['from']);
            $query->whereDate('published_at', '>=', $filters['from']);
        }
        if (!empty($filters['to'])) {
            $query->whereDate('published_at', '<=', $filters['to']);
        }

        // Sorting
        $sort = $filters['sort'] ?? 'published_at';
        $order = 'desc';
        if ($sort === 'oldest') {
            $sort = 'published_at';
            $order = 'asc';
        } elseif ($sort === 'title') {
            $order = 'asc';
        }

        $perPage = (int) ($filters['per_page'] ?? 20);
        $articles = $query->orderBy($sort, $order)->paginate($perPage);

        // Add filter data to pagination links
        if ($request->isMethod('post') || session()->has('article_filters')) {
            $articles->appends(['filtered' => 1]);
        }

        // Options for filters
        $sourcesList = Source::query()->orderBy('name')->get(['id','name','slug']);
        $categoriesList = Category::query()->orderBy('name')->get(['id','name','slug']);
        $authorsList = Author::query()->orderBy('name')->get(['id','name']);

        return view('articles.index', compact('articles', 'sourcesList', 'categoriesList', 'authorsList', 'filters'));
    }

    private function getFilters(Request $request): array
    {
        // If it's a POST request, validate and store filters in session
        if ($request->isMethod('post')) {
            $filters = $request->validate([
                'q' => ['nullable', 'string', 'max:255'],
                'sources' => ['nullable', 'array'],
                'sources.*' => ['string'],
                'categories' => ['nullable', 'array'],
                'categories.*' => ['string'],
                'authors' => ['nullable', 'array'],
                'authors.*' => ['string'],
                'from' => ['nullable', 'date'],
                'to' => ['nullable', 'date'],
                'sort' => ['nullable', 'string', 'in:,oldest,title'],
                'per_page' => ['nullable', 'integer', 'min:10', 'max:100'],
            ]);

            // Remove empty values
            $filters = array_filter($filters, function ($value) {
                if (is_array($value)) {
                    return !empty($value);
                }
                return $value !== null && $value !== '';
            });

            session(['article_filters' => $filters]);

            // Persist user preferences (sources, categories, authors) in a cookie (no login needed)
            $prefs = [
                'sources' => $filters['sources'] ?? [],
                'categories' => $filters['categories'] ?? [],
                'authors' => $filters['authors'] ?? [],
            ];
            Cookie::queue('article_prefs', json_encode($prefs), 60 * 24 * 30); // 30 days
            return $filters;
        }

        // For GET requests, check if we should clear filters or use session data
        if ($request->has('clear_filters')) {
            session()->forget('article_filters');
            return [];
        }

        // Use session filters if available
        $filters = session('article_filters', []);

        // If no filters in session, try to hydrate from cookie preferences
        if (empty($filters)) {
            $cookie = $request->cookie('article_prefs');
            if ($cookie) {
                $prefs = json_decode($cookie, true) ?: [];
                if (!empty($prefs)) {
                    $filters = array_merge($filters, array_filter([
                        'sources' => $prefs['sources'] ?? [],
                        'categories' => $prefs['categories'] ?? [],
                        'authors' => $prefs['authors'] ?? [],
                    ]));
                    session(['article_filters' => $filters]);
                }
            }
        }

        return $filters;
    }

    public function fetch(Request $request, AggregatorService $aggregator)
    {
        $params = array_filter($request->only(['q','category','from','to','page','pageSize']), fn($v) => $v !== null && $v !== '');
        $result = $aggregator->fetchAndStore($params);
        
        // Create professional user-friendly message
        $totalNew = $result['inserted'];
        $totalUpdated = $result['updated'];
        $totalProcessed = $totalNew + $totalUpdated;
        
        if ($totalProcessed === 0) {
            $message = "✓ Your news feed is up to date. No new articles found.";
        } elseif ($totalNew > 0 && $totalUpdated === 0) {
            $message = "✓ News updated successfully! {$totalNew} new " . ($totalNew === 1 ? 'article' : 'articles') . " added to your feed.";
        } elseif ($totalNew === 0 && $totalUpdated > 0) {
            $message = "✓ News refreshed successfully! {$totalUpdated} " . ($totalUpdated === 1 ? 'article' : 'articles') . " updated.";
        } else {
            $message = "✓ News updated successfully! {$totalNew} new and {$totalUpdated} updated articles.";
        }
        
        return redirect()->back()->with('status', $message);
    }
}
