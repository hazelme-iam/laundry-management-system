<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Load extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'weight',
        'status',
        'washer_machine_id',
        'dryer_machine_id',
        'washing_start',
        'washing_end',
        'drying_start',
        'drying_end',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'washing_start' => 'datetime',
        'washing_end' => 'datetime',
        'drying_start' => 'datetime',
        'drying_end' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function washerMachine()
    {
        return $this->belongsTo(Machine::class, 'washer_machine_id');
    }

    public function dryerMachine()
    {
        return $this->belongsTo(Machine::class, 'dryer_machine_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeWashing($query)
    {
        return $query->where('status', 'washing');
    }

    public function scopeDrying($query)
    {
        return $query->where('status', 'drying');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    // Helper methods
    public function getWashDurationAttribute()
    {
        return $this->wash_start && $this->wash_end 
            ? $this->wash_end->diffInMinutes($this->wash_start) 
            : null;
    }

    public function getDryDurationAttribute()
    {
        return $this->dry_start && $this->dry_end 
            ? $this->dry_end->diffInMinutes($this->dry_start) 
            : null;
    }

    public function getTotalDurationAttribute()
    {
        $start = $this->wash_start ?: $this->dry_start ?: $this->folding_start;
        $end = $this->folding_end ?: $this->dry_end ?: $this->wash_end;
        
        return $start && $end ? $end->diffInMinutes($start) : null;
    }

    // Load optimization methods
    public function getRemainingCapacityAttribute()
    {
        $machineCapacity = $this->washerMachine?->capacity_kg ?? 8.0;
        return $machineCapacity - $this->weight;
    }

    public function canAcceptAdditionalWeight($additionalWeight)
    {
        return $this->remaining_capacity >= $additionalWeight;
    }

    public function getEfficiencyScoreAttribute()
    {
        return $this->capacity_utilization ?? 0;
    }

    // Scopes for load optimization
    public function scopeHasCapacity($query, $requiredWeight)
    {
        return $query->whereRaw('(capacity_kg - weight) >= ?', [$requiredWeight]);
    }

    public function scopeUnderutilized($query, $threshold = 70)
    {
        return $query->where('capacity_utilization', '<', $threshold);
    }

    public function scopeConsolidated($query)
    {
        return $query->where('is_consolidated', true);
    }
}
