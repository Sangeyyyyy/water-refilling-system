<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'building',
        'division_id',
        'gallon_count',
    ];

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function ppmps()
    {
        return $this->hasMany(Ppmp::class);
    }

    public function latestApprovedPpmp()
    {
        return $this->hasOne(Ppmp::class)->where('status', 'approved')->orderBy('fiscal_year', 'desc');
    }

    /**
     * Get the full hierarchy display for the office.
     * Campus > College/Office > Division > Unit
     */
    public function getHierarchyAttribute()
    {
        $parts = [];
        
        $division = $this->division;
        if ($division) {
            $collegeOffice = $division->collegeOffice;
            if ($collegeOffice) {
                if ($collegeOffice->campus) {
                    $parts[] = $collegeOffice->campus->name;
                }
                $parts[] = $collegeOffice->name;
            } elseif ($division->campus) {
                // Fallback for divisions directly under campus
                $parts[] = $division->campus->name;
            }
            
            if (!$division->isPlaceholder()) {
                $parts[] = $division->name;
            }
        }
        
        $parts[] = $this->name;
        
        return implode(' > ', $parts);
    }
}
