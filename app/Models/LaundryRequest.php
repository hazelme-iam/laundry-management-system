<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaundryRequest extends Model
{
    use HasFactory;

    protected $table = 'laundry_requests';

    protected $fillable = [
        'customer_id',
        'status',
        'weight',
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
        'updated_by',
        'order_id',
    ];

    protected $casts = [
        'add_ons' => 'array',
        'pickup_date' => 'date',
        'estimated_finish' => 'datetime',
        'finished_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
