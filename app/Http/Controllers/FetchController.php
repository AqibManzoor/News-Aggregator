<?php

namespace App\Http\Controllers;

use App\Services\AggregatorService;
use Illuminate\Http\Request;

class FetchController extends Controller
{
    public function __invoke(Request $request, AggregatorService $aggregator)
    {
        $params = $request->only(['q','category','from','to','language','pageSize']);
        $count = $aggregator->aggregate(array_filter($params, fn($v) => $v !== null && $v !== ''));
        return response()->json(['fetched' => $count]);
    }
}
