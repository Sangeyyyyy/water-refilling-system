<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ppmp extends Model
{
    use HasFactory;

    protected $fillable = [
        'office_id',
        'budget_code',
        'ppmp_type',
        'description',
        'fiscal_year',
        'total_budget',
        'remaining_budget',
        'president_approved_date',
        'fund_manager',
        'fund_manager_email',
        'status',
    ];

    protected $casts = [
        'president_approved_date' => 'datetime',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function items()
    {
        return $this->hasMany(PpmpItem::class);
    }
}
