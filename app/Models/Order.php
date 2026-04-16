<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $prefix = Setting::get('order_prefix', 'HST');
            $dateCode = now()->format('mdy');
            $monthKey = "order_sequence_" . now()->format('Y_m');
            
            // Get atomic sequence for this month
            $count = Setting::getAndIncrement($monthKey, 0);
            
            $order->reference_number = "{$prefix}-{$dateCode}-{$count}";
        });
    }

    protected $fillable = [
        'reference_number',
        'user_id',
        'client_id',
        'client_name',
        'first_name',
        'last_name',
        'customer_type',
        'pr_number',
        'budget_code',
        'ppmp_id',
        'office_id',
        'other_location',
        'contact_number',
        'quantity',
        'total_amount',
        'order_type',
        'status',
        'delivery_date',
        'delivered_at',
        'is_refill',
        'container_ownership',
        'inventory_deducted',
        'budget_deducted',
        'missing_caps_count',
        'remarks',
    ];

    protected $casts = [
        'delivery_date' => 'date',
        'delivered_at' => 'datetime',
        'is_refill' => 'boolean',
        'inventory_deducted' => 'boolean',
        'budget_deducted' => 'boolean',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function office()
    {
        return $this->belongsTo(Office::class);
    }

    public function ppmp()
    {
        return $this->belongsTo(Ppmp::class);
    }

    public function getCampusAttribute()
    {
        return $this->office->division->campus->name ?? 'N/A';
    }

    public function getDivisionAttribute()
    {
        $division = $this->office->division ?? null;
        
        // Return null if division is a placeholder
        if ($division && $division->isPlaceholder()) {
            return null;
        }
        
        return $division ? $division->name : 'N/A';
    }

    /**
     * Get formatted hierarchy for display (e.g., "Main Campus > Office of the President > Unit")
     */
    public function getHierarchyDisplayAttribute()
    {
        if (!$this->office) {
            return $this->other_location ?? 'N/A';
        }

        $parts = [];
        
        // Add campus
        if ($this->office->division && $this->office->division->campus) {
            $parts[] = $this->office->division->campus->name;
        }
        
        // Add division only if not a placeholder
        if ($this->office->division && !$this->office->division->isPlaceholder()) {
            $parts[] = $this->office->division->name;
        }
        
        // Add office/unit name
        $parts[] = $this->office->name;
        
        return implode(' > ', $parts);
    }

    /**
     * Get the client's full name.
     */
    public function getFullClientNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return "{$this->first_name} {$this->last_name}";
        }
        return $this->client_name;
    }

    /**
     * Reusable search scope for multiple columns and relationships
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('orders.id', 'like', "%{$term}%")
              ->orWhere('orders.reference_number', 'like', "%{$term}%")
              ->orWhere('orders.first_name', 'like', "%{$term}%")
              ->orWhere('orders.last_name', 'like', "%{$term}%")
              ->orWhere('orders.client_name', 'like', "%{$term}%")
              ->orWhere('orders.contact_number', 'like', "%{$term}%")
              ->orWhere('orders.pr_number', 'like', "%{$term}%")
              ->orWhereHas('office', fn($oq) => $oq->where('name', 'like', "%{$term}%"));
        });
    }
}
