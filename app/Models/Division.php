<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = ['college_office_id', 'campus_id', 'name'];

    public function collegeOffice()
    {
        return $this->belongsTo(CollegeOffice::class);
    }

    public function campus()
    {
        return $this->belongsTo(Campus::class);
    }

    public function offices()
    {
        return $this->hasMany(Office::class);
    }

    /**
     * Check if this division is a placeholder (like "No Division", "N/A", etc.)
     */
    public function isPlaceholder()
    {
        $placeholders = ['no division', 'n/a', 'general', 'none', 'default'];
        return in_array(strtolower($this->name), $placeholders);
    }

    /**
     * Get the display name for hierarchy (returns null if placeholder)
     */
    public function getDisplayNameAttribute()
    {
        return $this->isPlaceholder() ? null : $this->name;
    }
}
