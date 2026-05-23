<?php

namespace App\Models;

use App\Models\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UniversityImage extends Model
{
    /** @use HasFactory<\Database\Factories\UniversityImageFactory> */
    use HasFactory, HasPublicId;

    protected $fillable = [
        'public_id',
        'university_id',
        'path_main',
        'path_thumb',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }
}
