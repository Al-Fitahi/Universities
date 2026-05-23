<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait HasPublicId
{

    protected static function bootHasPublicId(): void
    {
        static::creating(function ($model) {
            if (empty($model->public_id)) {
                $model->public_id = Str::ulid();
            }
        });
    }
}
