<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasPublicId;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory,HasPublicId;

    protected $with = ['project'];

        protected $fillable = [
    'public_id',
    'F_name',
    'S_name',
    'Th_name',
    'Su_name',
    'phone_number',
    'graduation_date',
    'graduation_grade',
    'certificate_image',
    'project_id',
    ];
        protected $hidden = ['id'];

public function getRouteKeyName(): string
    {
        return 'public_id';
    }

          public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function getFullNameAttribute(): string
{
    return "{$this->F_name} {$this->S_name} {$this->Th_name} {$this->Su_name}";
}
    public static function findOrCreateByFullName(array $data): Student
    {
        $attributes = [
            'F_name'  => $data['F_name'],
            'S_name'  => $data['S_name'],
            'Th_name' => $data['Th_name'],
            'Su_name' => $data['Su_name'],
        ];

        $values = [
            'phone_number'      => $data['phone_number'] ?? null,
            'graduation_date'   => $data['graduation_date'] ?? null,
            'graduation_grade'  => $data['graduation_grade'] ?? null,
            'certificate_image' => $data['certificate_image'] ?? null,
        ];

        $student = self::where($attributes)->first();

        if ($student) {
            $updated = false;

            foreach (['phone_number', 'graduation_date', 'graduation_grade', 'certificate_image'] as $key) {
                if (array_key_exists($key, $data)) {
                    $student->{$key} = $data[$key];
                    $updated = true;
                }
            }

            if ($updated) {
                $student->save();
            }

            return $student;
        }

        return self::create(array_merge($attributes, $values));
    }

}
