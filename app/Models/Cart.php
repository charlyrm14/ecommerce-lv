<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection as SupportCollection;

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

    /**
     * Stores or updates items in the given cart.
     *
     * Iterates through the provided list of products and inserts or updates
     * each item in the cart based on the product ID.
     *
     * @param \App\Models\Cart $cart The cart instance where the items will be stored.
     * @param array $requestedProducts An array of product data to be added or updated in the cart.
     *
     * @return void
 */
    public static function storeItems(Cart $cart, array $requestedProducts): void
    {
        foreach ($requestedProducts as $item) {
            $cart->cartItems()->updateOrCreate(['cart_id' => $cart->id, 'product_id' => $item['product_id']], $item);
        }
    }
}
