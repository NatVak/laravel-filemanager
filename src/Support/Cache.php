<?php

namespace Ybaruchel\LaravelFileManager\Support;

use Illuminate\Support\Facades\Cache as CacheFacade;

/**
 * -------------------------
 *| Core Cache Helper Class |
 * -------------------------
 */

class Cache extends CacheFacade
{
    public static function remember($key, $minutes, $callback)
    {
        return config('file-manager.use_cache') ? Cache::remember($key, $minutes, $callback) : call_user_func($callback);
    }
}