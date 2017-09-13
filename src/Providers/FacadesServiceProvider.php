<?php

namespace Ybaruchel\LaravelFileManager\Providers;

use ReflectionClass;
use Illuminate\Support\ServiceProvider;
use Ybaruchel\LaravelFileManager\Services\Cropper\CropperService;
use Ybaruchel\LaravelFileManager\Services\FileManager\FileManagerService;

class FacadesServiceProvider extends ServiceProvider
{
    /**
     * App entities
     * @var array
     */
    private $services = [
        'fileManager' => [
            'service' => FileManagerService::class,
            'dependencies' => [
            ]
        ],
        'cropper' => [
            'service' => CropperService::class,
            'dependencies' => [
            ]
        ],
    ];
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        foreach ($this->services as $serviceName => $serviceData) {
            $this->app->bind($serviceName, function ($app) use ($serviceData) {
                $dependencies = array_map(function ($dependency) use ($app) {
                    return $app->make($dependency);
                }, $serviceData['dependencies']);
                $classReflection = new ReflectionClass($serviceData['service']);
                return $classReflection->newInstanceArgs($dependencies);
            });
        }
    }
}