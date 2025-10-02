<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Source;
use App\Http\Resources\SourceResource;
use Illuminate\Http\JsonResponse;

class SourceController extends Controller
{
    public function __construct() {}

    /**
     * Display a listing of sources.
     */
    public function index(): JsonResponse
    {
        $sources = Source::query()
            ->withCount('articles')
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'external_id', 'website_url']);
        return response()->json(['data' => SourceResource::collection($sources)]);
    }
}
