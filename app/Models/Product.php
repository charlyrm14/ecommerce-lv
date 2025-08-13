<?php

namespace App\Models;

use App\Services\UtilsService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function ($product) {
            $product->uuid = (string) Str::uuid();
            $product->sku = UtilsService::generateSku($product->name);
        });
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

    /**
     * The scopeById function filters a query by the specified id value.
     *
     * @param Builder query The `` parameter is an instance of the
     * `Illuminate\Database\Eloquent\Builder` class, which is used for building database queries in
     * Laravel's Eloquent ORM.
     * @param int id The `id` parameter is an integer value that is used to filter the query results
     * based on the specified ID.
     */
    public function scopeById(Builder $query, int $id): void
    {
        $query->where('id', $id);
    }

    /**
     * The scopeByUuid function filters a query by the specified id value.
     * 
     * @param Builder query The `` parameter is an instance of the
     * `Illuminate\Database\Eloquent\Builder` class, which is used for building database queries in
     * Laravel's Eloquent ORM.
     * @param int id The `id` parameter is an integer value that is used to filter the query results
     * based on the specified ID.
     */
    public function scopeByUuid(Builder $query, string $uuid): void
    {
        $query->where('uuid', $uuid);
    }
}
