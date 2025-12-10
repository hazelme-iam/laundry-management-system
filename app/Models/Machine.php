<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Machine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'capacity_kg',
        'status',
        'notes',
    ];

    protected $casts = [
        'capacity_kg' => 'decimal:2',
    ];

    // Relationships
    public function washerLoads()
    {
        return $this->hasMany(Load::class, 'washer_machine_id');
    }

    public function dryerLoads()
    {
        return $this->hasMany(Load::class, 'dryer_machine_id');
    }

    public function primaryWashOrders()
    {
        return $this->hasMany(Order::class, 'primary_washer_id');
    }

    public function primaryDryOrders()
    {
        return $this->hasMany(Order::class, 'primary_dryer_id');
    }

    // Scopes
    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeWashers($query)
    {
        return $query->where('type', 'washer');
    }

    public function scopeDryers($query)
    {
        return $query->where('type', 'dryer');
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }
}
