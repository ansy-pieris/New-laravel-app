<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'category_id','name','slug','description','price','stock','image','added_by','is_active','is_featured'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function category()   { return $this->belongsTo(Category::class, 'category_id', 'category_id'); }
    public function creator()    { return $this->belongsTo(User::class, 'added_by'); }
    public function cartItems()  { return $this->hasMany(CartItem::class, 'product_id', 'product_id'); }
    public function orderItems() { return $this->hasMany(OrderItem::class, 'product_id', 'product_id'); }

    // Route model binding by slug
    public function getRouteKeyName() { return 'slug'; }

    // Auto-generate unique slug on create (immutable by default)
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->slug)) {
                // product_id isn't assigned yet, so start with name
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

    /**
     * Get the product image URL with fallback
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // First check if symlink exists, create if missing
            $this->ensureStorageSymlink();
            
            $imagePath = public_path('storage/products/' . $this->image);
            if (file_exists($imagePath)) {
                return asset('storage/products/' . $this->image);
            }
            
            // Fallback: try direct storage path
            $storagePath = storage_path('app/public/products/' . $this->image);
            if (file_exists($storagePath)) {
                return asset('storage/products/' . $this->image);
            }
        }
        
        // Fallback to placeholder image - use logo as last resort
        if (file_exists(public_path('images/placeholder.jpg'))) {
            return asset('images/placeholder.jpg');
        }
        
        // Ultimate fallback - use logo
        return asset('images/logo.png');
    }

    /**
     * Check if product has a valid image
     */
    public function hasImage()
    {
        if (!$this->image) return false;
        
        $imagePath = public_path('storage/products/' . $this->image);
        if (file_exists($imagePath)) return true;
        
        // Fallback: check storage path directly
        $storagePath = storage_path('app/public/products/' . $this->image);
        return file_exists($storagePath);
    }

    /**
     * Ensure storage symlink exists (Windows compatible)
     */
    private function ensureStorageSymlink()
    {
        $linkPath = public_path('storage');
        $targetPath = storage_path('app/public');
        
        // Check if symlink already exists and is working
        if (is_link($linkPath) && is_dir($linkPath)) {
            return;
        }
        
        // Remove broken symlink if exists
        if (file_exists($linkPath) || is_link($linkPath)) {
            if (PHP_OS_FAMILY === 'Windows') {
                exec("rmdir /s /q \"$linkPath\" 2>nul");
            } else {
                unlink($linkPath);
            }
        }
        
        // Create new symlink
        try {
            if (PHP_OS_FAMILY === 'Windows') {
                // Use Windows mklink command
                $relativePath = '..\\storage\\app\\public';
                $command = "mklink /D \"$linkPath\" \"$relativePath\"";
                exec($command);
            } else {
                symlink($targetPath, $linkPath);
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to create storage symlink: ' . $e->getMessage());
        }
    }
}
