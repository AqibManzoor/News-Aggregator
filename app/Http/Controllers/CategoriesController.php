<?php

namespace App\Http\Controllers;

use App\Models\Category;

class CategoriesController extends Controller
{
    public function index()
    {
        return response()->json(
            Category::query()->select(['id','name','slug'])->orderBy('name')->get()
        );
    }
}
