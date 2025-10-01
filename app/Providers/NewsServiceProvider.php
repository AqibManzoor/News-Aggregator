<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class NewsServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     */
    protected $defer = true;

    public function register(): void
    {
        // Lazily build the list of enabled news providers from config
        $this->app->singleton('news.providers', function () {
            $providers = [];
            $configs = config('news.providers', []);

            foreach ($configs as $key => $cfg) {
                if (($cfg['enabled'] ?? false) && !empty($cfg['class'])) {
                    try {
                        $providers[] = $this->app->make($cfg['class']);
                    } catch (\Throwable $e) {
                        // Skip providers that can't be instantiated
                    }
                }
            }

            return $providers;
        });
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return ['news.providers'];
    }
}
