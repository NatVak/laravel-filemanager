<?php

namespace Ybaruchel\LaravelFileManager\Traits;

use Illuminate\Http\UploadedFile;
use Intervention\Image\Facades\Image;
use Ybaruchel\LaravelFileManager\Models\File;
use Ybaruchel\LaravelFileManager\Models\Folder;
use Illuminate\Support\Facades\File as FileFacade;

trait FileManagerTrait
{
    /**
     * Method for removing items according to type.
     * @param $itemId
     * @param $itemType
     */
    private function removeItem($itemId, $itemType)
    {
        switch($itemType) {
            case 'file':
                $file = File::find($itemId);
                if($file)
                    $file->delete();
                break;
            case 'folder':
                $folder = Folder::find($itemId);
                if($folder)
                    $folder->delete();
                break;
        }
    }

    /**
     * Method for moving files and folders from
     * one location to another.
     *
     * @param $itemId
     * @param $itemType
     * @param $transferToFolder
     */
    private function moveItem($itemId, $itemType, $transferToFolder)
    {
        switch($itemType) {
            case 'file':
                $file = File::find($itemId);
                if ($file)
                    $file->update([
                        'parent_id' => ($transferToFolder == '0') ? NULL : $transferToFolder,
                    ]);
                break;
            case 'folder':
                $folder = Folder::find($itemId);
                if ($folder)
                    $folder->update([
                        'parent_id' => ($transferToFolder == '0') ? NULL : $transferToFolder,
                    ]);
                break;
        }
    }

    /**
     * Method for resize images and storing them.
     *
     * @param UploadedFile $file - The source file.
     * @param $size array - The desired image dimensions ex: ['width' => 200, 'height' => 200]
     * @param $originalFilename - The name of the original size file.
     * @param $folder - The destination folder for the current dimension.
     */
    private function resizeAndStore(UploadedFile $file, $size, $originalFilename, $folder)
    {
        $destinationPath = public_path('uploads/' . $folder . '/');
        if (!FileFacade::isDirectory($destinationPath))
            FileFacade::makeDirectory($destinationPath);
        $image  = Image::make($file->getRealPath())->fit($size['width'], $size['height'], function($constraint) {
            $constraint->upsize();
        });
        $image->save($destinationPath . $originalFilename);
    }

    /**
     * Method for formatting breadcrumbs
     *
     * @param $folder
     * @param $popup
     * @return array|null
     */
    private function _formatBreadcrumbs($folder, $popup)
    {
        if ($folder) {
            $parentsBreadcrumbs =array_map(function($p) use ($popup) {
                return ['title' => $p['name'], 'url' => file_manager_url('admin/file-manager/'.$p['id'])];
            }, $folder->getParents() );
            $parentsBreadcrumbs[] = [
                'title' => $folder->name,
                'url' => file_manager_url('admin/file-manager/'.$folder->id),
            ];
        } else {
            $parentsBreadcrumbs = null;
        }
        return $parentsBreadcrumbs;
    }

    /**
     * Method for checking if a given file is an image
     *
     * @param UploadedFile $file
     * @return bool
     */
    private function _isImage(UploadedFile $file)
    {
        return (@is_array(getimagesize($file))) ? true : false;
    }

    /**
     * Method for getting folders tree for select-box except for
     *
     * givens folder id.
     * @param array $foldersId
     * @param $currentFolder
     * @return array
     */
    private function _getTree($foldersId = [], $currentFolder)
    {
        $firstFolders = Folder::whereNull('parent_id')->get();
        $options = ($currentFolder != 0) ? ['<option value="" disabled selected>'.trans('admin.file-manager.browser.select_folder').'</option><option value="0">'.trans('admin.file-manager.main_folder').'</option>'] : ['<option value="" disabled selected>'.trans('admin.file-manager.browser.select_folder').'</option>'];
        foreach ($firstFolders as $folder) {
            if (!in_array($folder->id, $foldersId)) {
                if ($currentFolder != $folder->id) {
                    $options[] = '<option value="'.$folder->id.'">'.$folder->name.'</option>';
                }
                $folder->getChildren($foldersId, $currentFolder, $options);
            }
        }
        return $options;
    }
}