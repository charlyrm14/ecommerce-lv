<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'status',
        'cart_token'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:Y-m-d',
            'updated_at' => 'datetime:Y-m-d',
        ];
    }

    /**
     * The function users() returns a HasMany relationship with the User model
     * in PHP.
     *
     * @return HasMany A HasMany relationship is being returned.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * The function cartItems() returns a HasMany relationship with the CartItem model
     * in PHP.
     *
     * @return HasMany A HasMany relationship is being returned.
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
