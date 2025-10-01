<?php

namespace App\Http\Controllers;

use App\Models\Source;

class SourcesController extends Controller
{
    public function index()
    {
        return response()->json(
            Source::query()->select(['id','name','slug'])->orderBy('name')->get()
        );
    }
}
