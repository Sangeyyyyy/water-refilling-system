<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PpmpItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'ppmp_id',
        'description',
        'unit',
        'mode_of_procurement',
        'quantity',
        'unit_price',
        'total_price',
        'q1',
        'q2',
        'q3',
        'q4',
    ];

    public function ppmp()
    {
        return $this->belongsTo(Ppmp::class);
    }
}
