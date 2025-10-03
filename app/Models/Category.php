<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $primaryKey = 'category_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = ['name','slug'];

    public function products() {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    // Route model binding by slug
    public function getRouteKeyName() { return 'slug'; }

    // Auto-generate unique slug on create (immutable by default)
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = static::uniqueSlug(Str::slug($model->name));
            }
        });
    }

    protected static function uniqueSlug($base)
    {
        $slug = $base; $i = 1;
        while (static::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }
        return $slug;
    }
}
