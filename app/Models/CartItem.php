<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'total'
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
     * The function cart() returns a belongsTo relationship with the Cart model
     * in PHP.
     *
     * @return belongsTo A belongsTo relationship is being returned.
     */
    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * The function product() returns a belongsTo relationship with the Product model
     * in PHP.
     *
     * @return belongsTo A belongsTo relationship is being returned.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
