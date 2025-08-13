<?php

namespace App\Models;

use App\Observers\ProductObserver;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([ProductObserver::class])]
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
    
    /**
     * The function getById retrieves an object by its ID if it exists.
     *
     * @param int id The parameter `id` in the `getById` function is an integer that represents the
     * unique identifier of the object you want to retrieve.
     *
     * @return ?self The `getById` method is returning an instance of the class that it belongs to, or
     * `null` if no instance is found with the specified ID.
     */
    public static function getById(int $id): ?self
    {
        return static::find($id);
    }

    /**
     * The scopeByUuid function filters a query by a specific UUID value.
     *
     * @param Builder query The `` parameter is an instance of the Laravel query builder class
     * `Illuminate\Database\Eloquent\Builder`. It is used to build and execute database queries in an
     * object-oriented way.
     * @param string uuid The `uuid` parameter is a string that represents a universally unique
     * identifier (UUID). It is used to uniquely identify a specific entity or resource in a system.
     */
    public function scopeByUuid(Builder $query, string $uuid): void
    {
        $query->where('uuid', $uuid);
    }
}
