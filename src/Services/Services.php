<?php

namespace Ybaruchel\LaravelFileManager\Services;

class Services
{
    protected $app;

    public function __construct()
    {
        $this->app = app();
    }
}