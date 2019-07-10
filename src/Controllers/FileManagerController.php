<?php

namespace Ybaruchel\LaravelFileManager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Intervention\Image\Facades\Image;
use Ybaruchel\LaravelFileManager\Models\File;
use Ybaruchel\LaravelFileManager\Models\Folder;
use Illuminate\Support\Facades\File as FileFacade;
use Ybaruchel\LaravelFileManager\Traits\FileManagerTrait;

class FileManagerController extends Controller
{
    use FileManagerTrait;

    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Main method for showing view
     *
     * @url GET /admin/file-manager/{folderId}
     * @param $folderId - optional
     * @return \Illuminate\View\View
     */
    public function index($folderId = 0)
    {
        $folder = Folder::find($folderId);
        $popup = $this->request->get('popup', false);
        $selectOptions = $this->_getTree([], $folderId);
        $parentsBreadcrumbs = $this->_formatBreadcrumbs($folder, $popup);
        $files = ($folderId) ? File::where('parent_id', $folderId)->get() : File::whereNull('parent_id')->get();
        $folders = ($folderId) ? Folder::where('parent_id', $folderId)->get() : Folder::whereNull('parent_id')->get();

        return view('FileManager::file-manager.index')
            ->with('files', $files)
            ->with('popup', $popup)
            ->with('folder', $folder)
            ->with('folders', $folders)
            ->with('folderId', $folderId)
            ->with('selectOptions', $selectOptions)
            ->with('activeSidebar', 'file_manager')
            ->with('parentsBreadcrumbs', $parentsBreadcrumbs)
            ;
    }

    /**
     * Method for handling post request to main page,
     * this method process the moving of folders/files and
     * removing of folders/files.
     *
     * @url GET /admin/file-manager/{folderId}
     * @param $folderId - optional
     * @return \Illuminate\View\View
     */
    public function post($folderId = 0)
    {
        $remove           = $this->request->get('remove', false);
        $itemIds          = $this->request->get('itemID', false);
        $itemTypes        = $this->request->get('itemType', false);
        $transfer         = $this->request->get('transfer', false);
        $checkedItems     = $this->request->get('checkedItems', false);
        $transferToFolder = $this->request->get('transferToFolder', false);

        if (!$checkedItems)
            return back();

        // Filtering checked items
        $checkedItems = array_filter($checkedItems, function($item) {
            return $item == 1;
        });

        foreach ($checkedItems as $key => $itemId) {
            if (!isset($itemTypes[$key]))
                return back();
            if (!isset($itemIds[$key]))
                return back();
            if ($remove) {
                $this->removeItem($itemIds[$key], $itemTypes[$key]);
            } elseif ($transfer) {
                if($transferToFolder === false)
                    return back();
                $this->moveItem($itemIds[$key], $itemTypes[$key], $transferToFolder);
            }
        }
        return back();
    }

    /**
     * Method for renaming files and folders
     * names.
     *
     * @url POST /admin/file-manager/rename
     */
    public function rename()
    {
        $itemID = $this->request->get('itemID', false);
        $itemType = $this->request->get('itemType', false);
        $itemName = $this->request->get('itemName', false);
        $itemFolderDate = $this->request->get('itemFolderDate', false);

        if ($itemFolderDate)
        {
            Folder::where('id', $itemID)->update(['folder_date' => $itemFolderDate]);
        }
        if(!$itemID || !$itemType || !$itemName)
            return response()->json('error', 200);

        switch($itemType) {
            case 'file':
                $model = File::find($itemID);
                break;
            case 'folder':
                $model = Folder::find($itemID);
                break;
            default:
                return response()->json('error', 200);
                break;
        }

        if(!$model)
            return response()->json('error', 200);

        $model->update([
            'name' => $itemName,
        ]);
    }

    /**
     * Method for showing crop page
     *
     * @url GET /admin/file-manager/crop/{imagePath}
     * @param $imagePath
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function crop($imagePath)
    {
        $cropName       = $this->request->get('cropName');
        $cropNameExists = array_key_exists($cropName, config('file-manager.custom_crops'));
        if (!$cropName || !$cropNameExists) {
            return response(trans('file_manager::app.crop.crop_name_not_found'));
        }
        if (!FileFacade::exists(public_path('uploads/original/'.$imagePath))) {
            return response(trans('file_manager::app.crop.image_not_found'));
        }

        $image = Image::make(public_path('uploads/original/'.$imagePath));
        $imgWidth  = $image->width();
        $imgHeight = $image->height();
        $cropSizes = config('file-manager.custom_crops')[$cropName];
        foreach ($cropSizes as $cropSize => $values) {
            $croppedFile = 'uploads/crop/'. remove_extension_from_path($imagePath) . '_' . $cropName . '_' . $cropSize . '.' . get_extension_from_path($imagePath);
            $cropSizeExists = FileFacade::exists(public_path($croppedFile));
            $cropSizes[$cropSize]['fileExists'] = ($cropSizeExists) ? true : false;
            $cropSizes[$cropSize]['fileCroppedPath'] = url($croppedFile);
            $cropSizes[$cropSize]['canCrop'] = true;
            if($cropSizes[$cropSize]['width'] > $imgWidth)
            {
                $cropSizes[$cropSize]['canCrop'] = false;
                $cropSizes[$cropSize]['error'][] = trans('file_manager::app.crop.errors.crop_width_wider_than_image_width');
            }

            if($cropSizes[$cropSize]['height'] > $imgHeight)
            {
                $cropSizes[$cropSize]['canCrop'] = false;
                $cropSizes[$cropSize]['error'][] = trans('file_manager::app.crop.errors.crop_height_higher_than_image_height');
            }

            $cropSizes[$cropSize]['fromLeft'] = (($imgWidth - $cropSizes[$cropSize]['width']) / 2);
            $cropSizes[$cropSize]['fromTop']  = (($imgHeight - $cropSizes[$cropSize]['height']) / 2);
        }

        return view('FileManager::file-manager.crop')
            ->with('cropName', $cropName)
            ->with('imgWidth', $imgWidth)
            ->with('imgHeight', $imgHeight)
            ->with('imagePath', $imagePath)
            ->with('cropSizes', $cropSizes);
    }

    /**
     * Method for cropping images according to
     * input config parameters.
     *
     * @param $imagePath
     * @url POST /admin/file-manager/crop/{imagePath}
     * @return \Illuminate\Http\JsonResponse
     */
    public function doCrop($imagePath)
    {
        $req = $this->request->only('POST', 'MODFILES', 'image', 'cropName', 'itemId');

        $modCropArray = json_decode(html_entity_decode($req['MODFILES']), true);
        parse_str($req['POST'], $post);

        $DS = DIRECTORY_SEPARATOR;
        $imageDir =  public_path('uploads'.$DS.'original');
        $saveFolder = public_path('uploads'.$DS.'crop');
        $saveFolder = $saveFolder.$DS;

        $img = $imageDir.$DS.$imagePath;
        if(!file_exists($img))
            return response('No image');

        $image = Image::make($img);
        $image->backup();
        $imgWidth  = $image->width();
        $imgHeight = $image->height();

        $cutArray = array();
        foreach($post['_CROP'] as $cropKey => $cropData)
        {
            if(!isset($cropData['cutThis']) || $cropData['cutThis'] != 1)
                continue;

            if(!isset($modCropArray[$cropKey]))
                continue;

            $orig_w = $modCropArray[$cropKey]['width'];
            $orig_h = $modCropArray[$cropKey]['height'];
            $targ_x = $cropData['x'];
            $targ_y = $cropData['y'];
            $targ_w = $cropData['w'];
            $targ_h = $cropData['h'];

            $src = $img;
            $fileInfo = pathinfo($src);

            if($orig_w > $imgWidth)
                continue;

            if($orig_h > $imgHeight)
                continue;

            $newImageName = $saveFolder.$fileInfo['filename'].'_'.$req['cropName'].'_'.$cropKey.'.'.$fileInfo['extension'];

            $image->crop(floor($targ_w), floor($targ_h), floor($targ_x), floor($targ_y))
                ->resize($orig_w, $orig_h)
                ->save($newImageName);

            $image = $image->reset();
            $cutArray[] = $cropKey;
        }

        if (count($cutArray)) {
            $return  = array(
                'message'  => array(
                    'closeButton' => false,
                    'timeOut' => 1500,
                ),
                'cutArray' => $cutArray,
                'success' => 1,
            );
        }
        return response()->json($return);
    }

    /**
     * Method for showing upload page
     *
     * @url GET /admin/file-manager/upload
     * @param $folderId - optional
     * @return \Illuminate\View\View
     */
    public function upload($folderId = 0)
    {
        $folder = Folder::find($folderId);
        $popup = $this->request->get('popup', false);
        $parentsBreadcrumbs = $this->_formatBreadcrumbs($folder, $popup);
        $popup = $this->request->get('popup', false);
        return view('FileManager::file-manager.upload')
            ->with('popup', $popup)
            ->with('folderId', $folderId)
            ->with('activeSidebar', 'file_manager')
            ->with('parentsBreadcrumbs', $parentsBreadcrumbs)
            ;
    }

    /**
     * Method for uploading files to server
     *
     * @url GET /admin/file-manager/do-upload
     * @param $folderId - optional
     * @return \Illuminate\Http\JsonResponse
     */
    public function doUpload($folderId = 0)
    {
        $files = $this->request->file('files') ?: [];

        $storedFiles = [];
        // Storing files
        foreach ($files as $file)
        {
            // Storing file
            $newPath = $file->store('original', config('file-manager.disk'));
            $newFilename = str_replace('original/', '', $newPath);

            if($this->_isImage($file)) {
                // Getting crop types from config
                $cropTypes = config('file-manager.crop');
                // Cropping the different sizes
                foreach($cropTypes as $cropType => $values) {
                    $this->resizeAndStore($file, $values, $newFilename, $cropType);
                }
            }
            $newFile = File::create([
                'parent_id'   => ($folderId) ? $folderId : null,
                'name'        => $file->getClientOriginalName(),
                'type'        => ($this->_isImage($file)) ? 'image' : 'file',
                'path'        => $newFilename,
                'extension'   => $file->getClientOriginalExtension(),
                'size'        => $file->getSize(),
            ]);
            $storedFiles[] = [
                'name'         => $newFile->path,
                'size'         => $newFile->size,
                'url'          => url('uploads/original/' . $newFilename),
                'thumbnailUrl' => $newFile->getThumbnail(),
                'extension'    => $newFile->extension,
            ];
        }

        return response()->json(['files' => $storedFiles]);
    }

    /**
     * Method for adding a new folder.
     *
     * @url GET /admin/file-manager/addFolder
     * @param $folderId - optional
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addFolder($folderId = 0)
    {
        if (!is_numeric($folderId))
            return back();

        Folder::create([
            'parent_id' => ($folderId) ? $folderId : NULL,
            'name' => trans('file_manager::app.browser.new_folder')
        ]);

        return back();
    }

    /**
     * Method for getting options for select-box by ajax
     * call.
     *
     * @url POST /admin/file-manager/get-select-options
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSelectOptions()
    {
        $foldersId = $this->request->get('foldersIds', []);
        $currentFolder = $this->request->get('currentFolder');
        return response()->json($this->_getTree($foldersId, $currentFolder));
    }
}