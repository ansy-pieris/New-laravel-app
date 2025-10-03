<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingAddress extends Model
{
    protected $table = 'shipping_addresses';
    protected $primaryKey = 'address_id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false; // only created_at

    protected $fillable = [
        'user_id','recipient_name','phone','address','city','postal_code','is_default','created_at'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'created_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
