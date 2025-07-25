<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Brand extends Model
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
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (Brand $brand) {

            $slug = Str::slug($brand->name);
            $exists = Brand::where('slug', $slug)->exists();

            if (!$exists) {
                $brand->slug = $slug;
            } else {
                $brand->slug = "{$slug}-{$brand->id}";
            }

            $brand->save();
        });
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
     * The scopeById function filters a query by a specific ID value.
     *
     * @param Builder query The `` parameter is an instance of the
     * `Illuminate\Database\Eloquent\Builder` class, which is used for building database queries in
     * Laravel's Eloquent ORM.
     * @param int id The "id" parameter is an integer value that is used to filter the query results
     * based on the specified ID.
     */
    public function scopeById(Builder $query, int $id): void
    {
        $query->where('id', $id);
    }

    /**
     * The scopeBySlug function filters a query by a specific slug value.
     *
     * @param Builder query The `` parameter is an instance of the
     * `Illuminate\Database\Eloquent\Builder` class, which is used for building database queries in
     * Laravel's Eloquent ORM.
     * @param string id The "string" parameter is an string value that is used to filter the query results
     * based on the specified slug.
     */
    public function scopeBySlug(Builder $query, string $slug): void
    {
        $query->where('slug', $slug);
    }
}
