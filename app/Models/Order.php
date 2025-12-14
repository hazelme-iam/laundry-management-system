<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\NotificationService;

class Order extends Model
{
    use HasFactory;

    protected static function booted()
    {
        static::updated(function (Order $order) {
            if ($order->wasChanged('status')) {
                NotificationService::orderStatusChanged($order);
            }
        });
    }

    protected $table = 'orders';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'customer_id',
        'status',
        'is_backlog',
        'weight',
        'confirmed_weight',
        'weight_confirmed_at',
        'weight_confirmed_by',
        'add_ons',
        'subtotal',
        'discount',
        'total_amount',
        'amount_paid',
        'pickup_date',
        'estimated_finish',
        'finished_at',
        'remarks',
        'created_by',
        'assigned_washer_id',
        'assigned_dryer_id',
        'washing_start',
        'washing_end',
        'drying_start',
        'drying_end',
        'updated_by',
        'primary_washer_id',
        'primary_dryer_id',
        'picked_up_at',
        'quality_check_start',
        'quality_check_end',
        'delivery_started_at',
        'delivery_completed_at',
        'total_loads',
        'weight_per_load',
        'priority',
        'service_type',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'add_ons' => 'array',
        'pickup_date' => 'date',
        'estimated_finish' => 'datetime',
        'finished_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'quality_check_start' => 'datetime',
        'quality_check_end' => 'datetime',
        'delivery_started_at' => 'datetime',
        'delivery_completed_at' => 'datetime',
        'washing_start' => 'datetime',
        'washing_end' => 'datetime',
        'drying_start' => 'datetime',
        'drying_end' => 'datetime',
        'weight_confirmed_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'weight' => 'decimal:2',
        'confirmed_weight' => 'decimal:2',
        'weight_per_load' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function weightConfirmedBy()
    {
        return $this->belongsTo(User::class, 'weight_confirmed_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function primaryWasher()
    {
        return $this->belongsTo(Machine::class, 'primary_washer_id');
    }

    public function primaryDryer()
    {
        return $this->belongsTo(Machine::class, 'primary_dryer_id');
    }

    public function assignedWasher()
    {
        return $this->belongsTo(Machine::class, 'assigned_washer_id');
    }

    public function assignedDryer()
    {
        return $this->belongsTo(Machine::class, 'assigned_dryer_id');
    }

    public function loads()
    {
        return $this->hasMany(Load::class);
    }

    // Scopes for enhanced status management
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePickedUp($query)
    {
        return $query->where('status', 'picked_up');
    }

    public function scopeWashing($query)
    {
        return $query->where('status', 'washing');
    }

    public function scopeDrying($query)
    {
        return $query->where('status', 'drying');
    }

    public function scopeReady($query)
    {
        return $query->where('status', 'ready');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeHighPriority($query)
    {
        return $query->where('priority', 'high');
    }

    public function scopeUrgent($query)
    {
        return $query->where('priority', 'urgent');
    }

    public function scopeExpress($query)
    {
        return $query->where('service_type', 'express');
    }

    // Helper methods
    public function canBePickedUp()
    {
        return in_array($this->status, ['approved', 'ready']);
    }

    public function isInProgress()
    {
        return in_array($this->status, ['picked_up', 'washing', 'drying', 'folding', 'quality_check']);
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function getEstimatedLoadsAttribute()
    {
        if ($this->weight_per_load) {
            return ceil($this->weight / $this->weight_per_load);
        }
        return $this->calculateOptimalLoads();
    }

    public function calculateOptimalLoads($machineCapacity = 8.0)
    {
        $weight = (float) $this->weight;
        
        // Handle small orders efficiently
        if ($weight <= 2.0) {
            // Very small orders can be consolidated
            return 1; // Will be consolidated with other orders
        }
        
        if ($weight <= $machineCapacity) {
            return 1; // Fits in one load
        }
        
        // For larger orders, calculate needed loads
        return ceil($weight / $machineCapacity);
    }

    public function canBeConsolidated()
    {
        return $this->weight <= 4.0; // Orders up to 4kg can be consolidated
    }

    public function getConsolidationPriorityAttribute()
    {
        // Smaller orders have higher priority for consolidation
        if ($this->weight <= 1.0) return 10; // Very high priority
        if ($this->weight <= 2.0) return 8;  // High priority
        if ($this->weight <= 3.0) return 6;  // Medium priority
        if ($this->weight <= 4.0) return 4;  // Low priority
        return 0; // Not eligible for consolidation
    }

    public function findOptimalLoadToJoin()
    {
        if (!$this->weight || !$this->canBeConsolidated()) {
            return null;
        }

        // Find existing loads with capacity
        return Load::where('status', 'pending')
            ->whereHas('washerMachine', function ($query) {
                $query->where('capacity_kg', '>=', $this->weight);
            })
            ->whereRaw('weight + ? <= (SELECT capacity_kg FROM machines WHERE id = washer_machine_id)', [$this->weight])
            ->orderBy('weight') // Prefer fuller loads first
            ->first();
    }

    public function isWeightConfirmed()
    {
        return $this->confirmed_weight !== null;
    }

    public function getEffectiveWeight()
    {
        return $this->confirmed_weight ?? $this->weight;
    }

    public function isFullyPaid()
    {
        // Pending orders should never be marked as fully paid
        if ($this->status === 'pending') {
            return false;
        }
        
        return $this->amount_paid >= $this->total_amount;
    }

    public function createOptimizedLoads()
    {
        // Check if loads already exist for this order
        if ($this->loads()->count() > 0) {
            return $this->loads;
        }
        
        $loads = collect();
        $machineCapacity = 8.0;
        $weight = (float) $this->getEffectiveWeight();
        
        // Try to consolidate with existing loads first
        if ($this->canBeConsolidated()) {
            $existingLoad = $this->findOptimalLoadToJoin();
            if ($existingLoad) {
                // Add to existing load
                $existingLoad->weight += $weight;
                $existingLoad->capacity_utilization = ($existingLoad->weight / $machineCapacity) * 100;
                $existingLoad->is_consolidated = true;
                $existingLoad->save();
                
                // Create load relationship record
                $loadOrder = new Load([
                    'order_id' => $this->id,
                    'washer_machine_id' => $existingLoad->washer_machine_id,
                    'weight' => $weight,
                    'capacity_utilization' => ($weight / $machineCapacity) * 100,
                    'is_consolidated' => true,
                    'status' => 'pending',
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);
                $loadOrder->save();
                
                return collect([$loadOrder]);
            }
        }
        
        // Create new loads if no consolidation possible
        $numberOfLoads = $this->calculateOptimalLoads($machineCapacity);
        $weightPerLoad = $weight / $numberOfLoads;
        
        for ($i = 0; $i < $numberOfLoads; $i++) {
            $loadWeight = ($i == $numberOfLoads - 1) 
                ? $weight - ($weightPerLoad * $i) // Last load gets remainder
                : $weightPerLoad;
            
            $load = new Load([
                'order_id' => $this->id,
                'weight' => $loadWeight,
                'status' => 'pending',
            ]);
            
            $load->save();
            $loads->push($load);
        }
        
        return $loads;
    }

    public function getTotalProcessingTimeAttribute()
    {
        $start = $this->picked_up_at ?: $this->created_at;
        $end = $this->finished_at ?: now();
        return $start->diffInHours($end);
    }
}
