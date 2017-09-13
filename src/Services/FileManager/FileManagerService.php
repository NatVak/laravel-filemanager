<?php

namespace Ybaruchel\LaravelFileManager\Services\FileManager;

use Ybaruchel\LaravelFileManager\Services\Services;

class FileManagerService extends Services
{
    /**
     * Register the routes for file manager.
     *
     * @param  array|null  $attributes
     * @return void
     */
    public function routes(array $attributes = [])
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        $attributes = array_merge([
            'middleware' => ['web'],
            'prefix' => 'file-manager',
            'namespace' => '\Ybaruchel\LaravelFileManager\Controllers'
        ], $attributes);

        $this->app['router']->group($attributes, function ($router) {

            $router->group(['prefix' => 'resources'], function ($router) {
                $router->get('{type}/{filename}', 'ResourcesController@show')->name('resources');
            });

            $router->get('move', 'FileManagerController@move')->name('filemanager.move');
            $router->get('crop/{imagePath}', 'FileManagerController@crop')->name('filemanager.crop');
            $router->post('crop/{imagePath}', 'FileManagerController@doCrop')->name('filemanager.doCrop');
            $router->post('remove', 'FileManagerController@remove')->name('filemanager.remove');
            $router->post('rename', 'FileManagerController@rename')->name('filemanager.rename');
            $router->get('{folderId?}/upload', 'FileManagerController@upload')->name('filemanager.upload');
            $router->post('{folderId?}/do-upload', 'FileManagerController@doUpload')->name('filemanager.do_upload');
            $router->get('{folderId?}/add-folder', 'FileManagerController@addFolder')->name('filemanager.add_folder');
            $router->post('get-select-options', 'FileManagerController@getSelectOptions')->name('filemanager.get_selected_options');
            $router->post('/{folderId?}', 'FileManagerController@post')->name('filemanager.main_post');
            $router->get('/{folderId?}', 'FileManagerController@index')->name('filemanager.main');
        });
    }
}