<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AggregatorService;
use Illuminate\Http\Request;

class FetchController extends Controller
{
    public function __invoke(Request $request, AggregatorService $aggregator)
    {
        $params = array_filter($request->only(['q','category','from','to','language','page','pageSize']), fn($v) => $v !== null && $v !== '');
        $result = $aggregator->fetchAndStore($params);
        return response()->json(['status' => 'ok'] + $result);
    }
}
