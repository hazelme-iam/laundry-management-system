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
        'status',
        'capacity_kg',
        'notes',
        'current_order_id',
        'washing_start',
        'washing_end',
        'drying_start',
        'drying_end',
    ];

    protected $casts = [
        'washing_start' => 'datetime',
        'washing_end' => 'datetime',
        'drying_start' => 'datetime',
        'drying_end' => 'datetime',
    ];

    // Relationships
    public function currentOrder()
    {
        return $this->belongsTo(Order::class, 'current_order_id');
    }

    // Scopes
    public function scopeIdle($query)
    {
        return $query->where('status', 'idle');
    }

    public function scopeInUse($query)
    {
        return $query->where('status', 'in_use');
    }

    public function scopeWashers($query)
    {
        return $query->where('type', 'washer');
    }

    public function scopeDryers($query)
    {
        return $query->where('type', 'dryer');
    }

    // Helper methods
    public function isAvailable()
    {
        return $this->status === 'idle';
    }

    public function getTimeRemaining()
    {
        if ($this->type === 'washer' && $this->washing_end) {
            return now()->diffInSeconds($this->washing_end, false);
        }
        
        if ($this->type === 'dryer' && $this->drying_end) {
            return now()->diffInSeconds($this->drying_end, false);
        }
        
        return null;
    }

    public function getTimeRemainingFormatted()
    {
        $seconds = $this->getTimeRemaining();
        if ($seconds === null || $seconds <= 0) {
            return null;
        }
        
        $minutes = ceil($seconds / 60);
        return "{$minutes} mins left";
    }
}
