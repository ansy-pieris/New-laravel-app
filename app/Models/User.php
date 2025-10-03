<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password',
        'role','phone','address','city','postal_code',
    ];

    protected $hidden = ['password','remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $attributes = [
        'role' => 'customer', // Default role for new users
    ];

    public function cartItems()   { return $this->hasMany(CartItem::class); }
    public function orders()      { return $this->hasMany(Order::class); }
    public function addresses()   { return $this->hasMany(ShippingAddress::class); }
    public function addedProducts() { return $this->hasMany(Product::class, 'added_by'); }

    /**
     * Check if user is an admin
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Check if user is staff
     */
    public function isStaff()
    {
        return $this->role === 'staff';
    }

    /**
     * Check if user is a customer
     */
    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    /**
     * Check if user has admin or staff privileges
     */
    public function hasAdminAccess()
    {
        return in_array($this->role, ['admin', 'staff']);
    }
}
