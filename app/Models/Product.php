<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'sku',
        'status',
        'category_id',
        'brand_id',
        'uuid'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'deleted_at'
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
     * The function brand() returns a belongsTo relationship with the Brand model
     * in PHP.
     *
     * @return belongsTo A belongsTo relationship is being returned.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * The function category() returns a belongsTo relationship with the Category model
     * in PHP.
     *
     * @return belongsTo A belongsTo relationship is being returned.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}
