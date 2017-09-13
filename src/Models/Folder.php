<?php

namespace Ybaruchel\LaravelFileManager\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    protected $table = 'folders';

    protected $fillable = [
        'parent_id',
        'name',
    ];

    protected static function boot()
    {
        parent::boot();
        static::deleting(function($folder) {
            foreach ($folder->files as $file) {
                $file->delete();
            }
            foreach ($folder->folders as $folders) {
                $folders->delete();
            }
        });
    }

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'parent_id', 'id');
    }

    public function folders()
    {
        return $this->hasMany(Folder::class, 'parent_id', 'id');
    }

    public function itemsCount()
    {
        return $this->hasMany(Folder::class, 'parent_id', 'id')->get()->count() +
        $this->hasMany(File::class, 'parent_id', 'id')->get()->count();
    }


    public function getSize()
    {
        $size = $this->getRecursiveSize();
        if(!$size)
            return '0B';
        $base = log($size, 1024);
        $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
        return round(pow(1024, $base - floor($base)), 1) . $suffixes[floor($base)];
    }

    /**
     * Method for getting folder size recursively.
     *
     * @param int $size - in bytes.
     * @return string
     */
    private function getRecursiveSize(&$size = 0) {
        $closestChildren = $this->folders;
        $closestFiles = $this->files;
        foreach ($closestFiles as $file) {
            $size += $file->size;
        }
        if ($closestChildren) {
            foreach ($closestChildren as $closestChild) {
                $closestChild->getRecursiveSize($size);
            }
        }
        return $size;
    }

    /**
     * Method for getting parents folders for breadcrumbs use.
     *
     * @param array $parents
     * @return array
     */
    public function getParents(&$parents = [])
    {
        $parent = $this->parent;
        if ($parent) {
            $parents[] = [
                'id'  => $parent->id,
                'name' => $parent->name,
            ];
            $parent->getParents($parents);
        }
        krsort($parents);
        return $parents;
    }

    /**
     * Method for getting children except of given folders ids
     * and current folder. for the select box.
     *
     * @param $foldersId
     * @param $currentFolder
     * @param $children
     * @param int $level - for making space effect that imitates hierarchy
     * @return array
     * @internal param $folderId
     */
    public function getChildren($foldersId, $currentFolder, &$children, &$level = 0)
    {
        $closestChildren = $this->folders;
        $level++;
        if ($closestChildren) {
            foreach ($closestChildren as $closestChild) {
                if (!in_array($closestChild->id, $foldersId)) {
                    if ($currentFolder != $closestChild->id) {
                        $children[] = '<option value="'.$closestChild->id.'">'. str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level) . $closestChild->name.'</option>';
                    }
                    $closestChild->getChildren($foldersId, $currentFolder, $children, $level);
                }
            }
        }
        $level--;
        return $children;
    }
}
