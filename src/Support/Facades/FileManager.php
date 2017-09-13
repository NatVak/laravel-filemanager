<?php

namespace Ybaruchel\LaravelFileManager\Support\Facades;

use \Illuminate\Support\Facades\Facade;

/**
 * Facade class to be called whenever the class FileManagerService is called
 */
class FileManager extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor() { return 'fileManager'; }
}