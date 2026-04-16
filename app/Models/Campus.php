<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campus extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function collegeOffices()
    {
        return $this->hasMany(CollegeOffice::class);
    }

    public function divisions()
    {
        return $this->hasManyThrough(Division::class, CollegeOffice::class);
    }
}
