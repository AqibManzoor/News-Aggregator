<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Http\Request;

class ArticlesController extends Controller
{
    public function index(Request $request)
    {
        dd('Use API endpoint /api/articles for fetching articles with filters.');
        $validated = $request->validate([
            'q' => ['nullable','string'],
            'from' => ['nullable','date'],
            'to' => ['nullable','date'],
            'sources' => ['nullable'],
            'categories' => ['nullable'],
            'authors' => ['nullable'],
            'per_page' => ['nullable','integer','min:1','max:100'],
            'sort' => ['nullable','in:published_at,title'],
            'order' => ['nullable','in:asc,desc'],
        ]);

        $perPage = (int)($validated['per_page'] ?? 20);
        $sort = $validated['sort'] ?? 'published_at';
        $order = $validated['order'] ?? 'desc';

        $query = Article::query()->with(['source:id,name,slug','categories:id,name,slug','authors:id,name']);

        if (!empty($validated['q'])) {
            $q = $validated['q'];
            $query->where(function ($w) use ($q) {
                $w->where('title', 'like', "%$q%")
                    ->orWhere('summary', 'like', "%$q%")
                    ->orWhere('content', 'like', "%$q%");
            });
        }

        if (!empty($validated['from'])) {
            $query->where('published_at', '>=', $validated['from']);
        }
        if (!empty($validated['to'])) {
            $query->where('published_at', '<=', $validated['to']);
        }

        // Sources can be array of slugs or ids
        $sources = $request->input('sources');
        if ($sources) {
            $sourcesArr = is_array($sources) ? $sources : explode(',', (string)$sources);
            $ids = Source::whereIn('slug', $sourcesArr)->pluck('id')->all();
            $ids = array_merge($ids, array_filter($sourcesArr, fn($s) => is_numeric($s)));
            if ($ids) {
                $query->whereIn('source_id', $ids);
            }
        }

        // Categories filter: by slugs or ids
        $categories = $request->input('categories');
        if ($categories) {
            $catArr = is_array($categories) ? $categories : explode(',', (string)$categories);
            $catIds = Category::whereIn('slug', $catArr)->pluck('id')->all();
            $catIds = array_merge($catIds, array_filter($catArr, fn($s) => is_numeric($s)));
            if ($catIds) {
                $query->whereHas('categories', function ($q) use ($catIds) {
                    $q->whereIn('categories.id', $catIds);
                });
            }
        }

        // Authors filter by names
        $authors = $request->input('authors');
        if ($authors) {
            $aArr = is_array($authors) ? $authors : explode(',', (string)$authors);
            $aArr = array_map('trim', $aArr);
            $query->whereHas('authors', function ($q) use ($aArr) {
                $q->whereIn('authors.name', $aArr);
            });
        }

        $query->orderBy($sort, $order);

        $paginator = $query->paginate($perPage);
        return response()->json($paginator);
    }
}
