<?php

namespace App\Observers;

use Illuminate\Support\Str;

class SlugObserver {

    /**
     * Handle the Media "created" event.
     */
    public function created($model): void
    {
        $this->generateSlug($model);
    }

    /**
     * Generates and assigns a unique slug for the given model based on its name.
     *
     * This method converts the model's `name` attribute into a URL-friendly slug.
     * If the generated slug already exists in the database for the same model type,
     * the model's `id` is appended to ensure uniqueness.
     *
     * @param \Illuminate\Database\Eloquent\Model $model The Eloquent model instance for which the slug should be generated.
     *
     * @return void
     */
    public function generateSlug($model): void
    {
        $slug = Str::slug($model->name);

        $exists = $model->newQuery()->where('slug', $slug)->exists();

        if (!$exists) {
            $model->slug = $slug;
        } else {
            $model->slug = "{$slug}-{$model->id}";
        }

        $model->save();
    }
}
