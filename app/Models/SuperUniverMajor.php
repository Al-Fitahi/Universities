<?php

namespace App\Models;

use App\Models\Traits\HasPublicId;
use App\Models\UniversityMajor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuperUniverMajor extends Model
{
    /** @use HasFactory<\Database\Factories\SuperUniverMajorFactory> */
    use HasFactory,HasPublicId;

protected $fillable = [
    'public_id',
    'university_major_id',
    'supervisor_id',
];
      protected $hidden = ['id'];

          public function universityMajor(): BelongsTo
    {
        return $this->belongsTo(UniversityMajor::class, 'university_major_id');
    }

          public function supervisor(): BelongsTo
    {
        return $this->belongsTo(Supervisor::class, 'supervisor_id');
    }

      public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }


}
