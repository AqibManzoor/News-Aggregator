<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    /**
     * Health check endpoint
     */
    public function check(): JsonResponse
    {
        $status = 'healthy';
        $checks = [];

        // Database check
        try {
            DB::connection()->getPdo();
            $checks['database'] = 'ok';
        } catch (\Exception $e) {
            $checks['database'] = 'error';
            $status = 'unhealthy';
        }

        // Cache check
        try {
            Cache::put('health_check', 'ok', 60);
            $cacheStatus = Cache::get('health_check');
            $checks['cache'] = $cacheStatus === 'ok' ? 'ok' : 'error';
        } catch (\Exception $e) {
            $checks['cache'] = 'error';
            $status = 'unhealthy';
        }

        // News providers check
        try {
            $providers = app('news.providers');
            $checks['providers'] = count($providers) > 0 ? 'ok' : 'warning';
        } catch (\Exception $e) {
            $checks['providers'] = 'error';
            $status = 'unhealthy';
        }

        return response()->json([
            'status' => $status,
            'timestamp' => now()->toISOString(),
            'checks' => $checks,
        ], $status === 'healthy' ? 200 : 503);
    }
}
