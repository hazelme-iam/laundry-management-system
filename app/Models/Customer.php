<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'customer_type',
        'total_orders',
        'total_spent',
        'last_order_at',
        'notes',
    ];

    protected $casts = [
        'last_order_at' => 'datetime',
        'total_spent'   => 'decimal:2',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
