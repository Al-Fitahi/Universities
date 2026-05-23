<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasPublicId;

class UniversityMajor extends Model
{
    /** @use HasFactory<\Database\Factories\UniversityMajorFactory> */
    use HasFactory,HasPublicId;

        protected $fillable = [
        'public_id',
        'number_of_seats',
        'admission_rate',
        'study_years',
        'tuition_fee',
        'major_id',
        'university_id',
        'published',
    ];

          protected $casts = [
        'published' => 'boolean',
        'published_at' => 'datetime',
    ];


      protected $hidden = ['id'];

    public function getRouteKeyName(): string
    {
        return 'public_id';
    }

    public function major()
    {
        return $this->belongsTo(Major::class);
    }

    public function university()
    {
        return $this->belongsTo(University::class);
    }

      public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function superUniverMajor()
{
  return $this->hasMany(SuperUniverMajor::class);
}


}
