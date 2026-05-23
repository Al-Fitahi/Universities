<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\HasPublicId;


class ProjectType extends Model
{
  /** @use HasFactory<\Database\Factories\ProjectTypeFactory> */
  use HasFactory, HasPublicId;

  protected $fillable = [
    'public_id',
    'name',
  ];


  public function projects()
  {
      return $this->hasMany(Project::class);
  }


  protected $hidden = ['id'];

  public function getRouteKeyName(): string
  {
    return 'public_id';
  }
}
