<?php

Route::group(['prefix' => 'file-manager', 'namespace' => 'Ybaruchel\LaravelFileManager\Controllers'], function() {
    Route::get('move', 'FileManagerController@move')->name('filemanager.move');
    Route::get('crop/{imagePath}', 'FileManagerController@crop')->name('filemanager.crop');
    Route::post('crop/{imagePath}', 'FileManagerController@doCrop')->name('filemanager.doCrop');
    Route::post('remove', 'FileManagerController@remove')->name('filemanager.remove');
    Route::post('rename', 'FileManagerController@rename')->name('filemanager.rename');
    Route::get('{folderId?}/upload', 'FileManagerController@upload')->name('filemanager.upload');
    Route::post('{folderId?}/do-upload', 'FileManagerController@doUpload')->name('filemanager.do_upload');
    Route::get('{folderId?}/add-folder', 'FileManagerController@addFolder')->name('filemanager.add_folder');
    Route::post('get-select-options', 'FileManagerController@getSelectOptions')->name('filemanager.get_selected_options');
    Route::post('/{folderId?}', 'FileManagerController@post')->name('filemanager.main_post');
    Route::get('/{folderId?}', 'FileManagerController@index')->name('filemanager.main');
});