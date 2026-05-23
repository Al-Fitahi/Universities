<?php

namespace App\Models;

use App\Models\ProjectType;
use App\Models\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory,HasPublicId;

    protected $with = ['projectType', 'superUniverMajor'];


    protected $fillable = [
        'public_id',
        'super_univer_major_id',
        'name',
        'project_type_id',
        'project_overview',
        'pdf_path',
        'is_active',
    ];
        public function projectType(): BelongsTo
    {
        return $this->belongsTo(ProjectType::class, 'project_type_id');
    }

            public function superUniverMajor(): BelongsTo
    {
        return $this->belongsTo(SuperUniverMajor::class, 'super_univer_major_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'project_id');
    }

      protected $hidden = ['id'];

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }
}

