<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\SourceController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\AuthorController;
use App\Http\Controllers\Api\FetchController;
use Illuminate\Support\Facades\Cookie;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Article endpoints
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/stats', [ArticleController::class, 'stats']);
Route::get('/articles/{article}', [ArticleController::class, 'show']);

// Source endpoints
Route::get('/sources', [SourceController::class, 'index']);

// Category endpoints
Route::get('/categories', [CategoryController::class, 'index']);

// Author endpoints
Route::get('/authors', [AuthorController::class, 'index']);

// Fetch endpoint (no auth per challenge spec)
Route::post('/fetch', FetchController::class);

// User preferences - simple cookie-based get/set
Route::get('/preferences', function (\Illuminate\Http\Request $request) {
    $prefs = json_decode($request->cookie('article_prefs', '{}'), true) ?: [];
    return response()->json(['data' => [
        'sources' => $prefs['sources'] ?? [],
        'categories' => $prefs['categories'] ?? [],
        'authors' => $prefs['authors'] ?? [],
    ]]);
});

Route::post('/preferences', function (\Illuminate\Http\Request $request) {
    $data = $request->validate([
        'sources' => ['nullable','array'],
        'sources.*' => ['string'],
        'categories' => ['nullable','array'],
        'categories.*' => ['string'],
        'authors' => ['nullable','array'],
        'authors.*' => ['string'],
    ]);
    $prefs = [
        'sources' => $data['sources'] ?? [],
        'categories' => $data['categories'] ?? [],
        'authors' => $data['authors'] ?? [],
    ];
    Cookie::queue('article_prefs', json_encode($prefs), 60 * 24 * 30);
    return response()->json(['status' => 'ok', 'data' => $prefs]);
});

