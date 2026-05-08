<?php

namespace App\Concerns\Cache;

use Illuminate\Support\Facades\Cache;

trait CacheTrait
{
    protected function cacheRemember(string $key, callable $callback, int $ttl = 60)
    {
        return Cache::remember($key, $callback, $ttl);
    }

    protected function cacheRememberForever(string $key, callable $callback)
    {

        return Cache::rememberForever($key, $callback);
    }

    protected function cacheForget($keys)
    {
        if (is_array($keys)) {
            $results = [];
            foreach ($keys as $key) {
                $results[$key] = Cache::forget($key);
            }
            return $results;
        }

        return Cache::forget($keys);
    }
}
