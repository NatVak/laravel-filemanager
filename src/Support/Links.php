<?php

namespace Ybaruchel\LaravelFileManager\Support;

use Ybaruchel\LaravelFileManager\Models\File;


/**
 *  ------------------------
 * | Links Helper Class |
 *  ------------------------
 */
class Links
{
    private static $imagesAlts = [];
    public static function getImageAlt($path)
    {
        if(array_get(self::$imagesAlts, $path))
            return self::$imagesAlts[$path];

        $img = Cache::remember('alt_image_'.$path, 'high', function() use($path) {
            return File::where('path', $path)->first();
        });

        return self::$imagesAlts[$path] = ($img ? $img->name : null);
    }
}