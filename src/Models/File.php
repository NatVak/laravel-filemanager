<?php

namespace Ybaruchel\LaravelFileManager\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File as FileFacade;

class File extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $table = 'files';

    protected $fillable = [
        'parent_id',
        'name',
        'description',
        'path',
        'extension',
        'size',
        'type'
    ];

    protected static function boot() {
        parent::boot();
        static::deleting(function($file) {
            $DS = DIRECTORY_SEPARATOR;
            // Deleting original file
            $uploadPath = public_path('uploads'.$DS);
            if (FileFacade::exists($uploadPath . 'original/' . $file->path))
                FileFacade::delete($uploadPath . 'original/' . $file->path);
            // Deleting crop images
            if (remove_extension_from_path($file->path)) {
                $cropPath = public_path('uploads'.$DS . 'crop');
                $glob = $cropPath . $DS . remove_extension_from_path($file->path) . '*';
                $fileCrops = FileFacade::glob($glob);
                foreach ($fileCrops as $fileCrop) {
                    if(FileFacade::exists($fileCrop))
                        FileFacade::delete($fileCrop);
                }
            }
            // Deleting config crop images
            $cropTypes = config('file-manager.crop');
            // Cropping the different sizes
            foreach($cropTypes as $cropType => $values) {
                if(FileFacade::exists($uploadPath . $cropType . $DS . $file->path))
                    FileFacade::delete($uploadPath . $cropType . $DS . $file->path);
            }
        });
    }

    public function parent() {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function getSize() {
        $base = log($this->size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
        return round(pow(1024, $base - floor($base)), 1) . $suffixes[floor($base)];
    }

    /**
     * Method for getting the image thumbnail for a non-image file.
     *
     * @return \Illuminate\Contracts\Routing\UrlGenerator|string
     */
    public function getThumbnail()
    {
        if($this->type == 'image') {
            return url('uploads/thumbnail/' . $this->path);
        }
        $extensionImage = public_path('assets/admin/images/file-manager/filestypes/' . $this->extension . '.png');
        if (FileFacade::exists($extensionImage))
            return url('assets/admin/images/file-manager/filestypes/' . $this->extension . '.png');
        else
            return url('assets/admin/images/file-manager/filestypes/file.png');
    }
}
