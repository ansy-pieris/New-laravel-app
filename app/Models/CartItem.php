<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $table = 'carts';
    protected $primaryKey = 'cart_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = ['user_id','product_id','product_image','quantity','added_at','total_price'];

    protected $casts = [
        'quantity'    => 'integer',
        'total_price' => 'decimal:2',
        'added_at'    => 'datetime',
    ];

    public function user()    { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class, 'product_id', 'product_id'); }
}
