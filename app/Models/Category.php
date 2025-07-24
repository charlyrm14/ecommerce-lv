<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
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
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::created(function (Category $category) {

            $slug = Str::slug($category->name);
            $exists = Category::where('slug', $slug)->exists();

            if (!$exists) {
                $category->slug = $slug;
            } else {
                $category->slug = "{$slug}-{$category->id}";
            }

            $category->save();
        });
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
}
