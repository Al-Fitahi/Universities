<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasPublicId;

class Supervisor extends Model
{
    /** @use HasFactory<\Database\Factories\SupervisorFactory> */
    use HasFactory,HasPublicId;

protected $fillable = [
    'public_id',
    'F_name',
    'S_name',
    'Th_name',
    'Su_name',
    'phone_number',
    'is_active',
];

      protected $hidden = ['id'];
    public function getRouteKeyName(): string
    {
        return 'public_id';
    }
        public function getFullNameAttribute(): string
{
    return "{$this->F_name} {$this->S_name} {$this->Th_name} {$this->Su_name}";
}

    public function superUniverMajor()
{
  return $this->hasMany(SuperUniverMajor::class);
}
}
