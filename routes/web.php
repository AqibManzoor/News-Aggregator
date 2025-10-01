<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\ArticleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [ArticleController::class, 'index'])->name('articles.index');
Route::post('/', [ArticleController::class, 'index'])->name('articles.filter');
Route::post('/fetch', [ArticleController::class, 'fetch'])->name('articles.fetch');
