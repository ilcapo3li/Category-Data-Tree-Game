<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public $fillable = [
        'title',
        'slug',
        'parent_id',
    ];

    protected $append = [
        'is_last',
        'parent_list',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function ancestors()
    {
        return $this->parent()->with('ancestors');
    }

    public function getParentListAttribute()
    {
        $list = collect([]);
        $category = $this->load('ancestors')->ancestors;
        $list->push($this);
        while (!is_null($category)) {
            $list->push($category);
            $category = $category->ancestors;
        }
        return $list;
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getIsLastChildAttribute()
    {
        return boolval(!count($this->children));
    }

    public function grandchildren()
    {
        return $this->children()->with('grandchildren');
    }
}
