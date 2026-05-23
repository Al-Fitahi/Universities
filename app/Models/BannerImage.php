<?php

namespace App\Models;

use App\Models\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BannerImage extends Model
{
    /** @use HasFactory<\Database\Factories\BannerImageFactory> */
    use HasFactory, HasPublicId;

    protected $fillable = [
        'public_id',
        'banner_id',
        'path_main',
        'path_thumb',
        'priority',
        'link_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class);
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }
}
