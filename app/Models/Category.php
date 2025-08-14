<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\CategoryFactory> */
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'status'
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
     * The function parent() returns a belongsTo relationship with the Category model
     * in PHP.
     *
     * @return belongsTo A belongsTo relationship is being returned.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * The function children() returns a HasMany relationship with the Category model
     * in PHP.
     *
     * @return HasMany A HasMany relationship is being returned.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * The function childrenRecursive() return a recursive relationship with the Category model
     * in PHP
     *
     * @return HasMany A HasMany relationship is being returned.
     */
    public function childrenRecursive(): HasMany
    {
        return $this->children()->with('childrenRecursive');
    }

    /**
     * The function products() returns a HasMany relationship with the Product model
     * in PHP.
     *
     * @return HasMany A HasMany relationship is being returned.
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }


    /**
     * The function getById retrieves a Category object by its ID if it exists.
     *
     * @param int id The parameter `id` is an integer value that represents the unique identifier of a
     * category.
     *
     * @return ?Category The `getById` function is returning an instance of the `Category` class with
     * the specified ID, or `null` if no category with that ID is found.
     */
    public static function getById(int $id): ?Category
    {
        return static::find($id);
    }

    /**
     * This PHP function defines a scope in Laravel Eloquent that filters query results by a specified
     * slug value.
     *
     * @param Builder query The `` parameter is an instance of the
     * `Illuminate\Database\Eloquent\Builder` class, which is used for building database queries in
     * Laravel's Eloquent ORM.
     * @param string slug The `slug` parameter is a string that is used to filter the query results
     * based on a specific slug value. The `scopeBySlug` function is a query scope that can be used to
     * apply this filter when querying the database.
     */
    public function scopeBySlug(Builder $query, string $slug): void
    {
        $query->where('slug', $slug);
    }
}
