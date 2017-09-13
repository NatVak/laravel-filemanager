<?php

namespace Ybaruchel\LaravelFileManager\Support\Facades;

use \Illuminate\Support\Facades\Facade;

/**
 * Facade class to be called whenever the class FileManagerService is called
 */
class Cropper extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor() { return 'cropper'; }
}