<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'order_number',
        'type',
        'status',
        'order_date',
        'pickup_time',
        'total_amount',
        'tax_amount',
        'special_instructions',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'datetime',
        'pickup_time' => 'datetime',
        'total_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
    ];

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if the order is a takeaway order.
     */
    public function isTakeaway()
    {
        return $this->type === 'takeaway';
    }

    /**
     * Check if the order is a dine-in order.
     */
    public function isDineIn()
    {
        return $this->type === 'dine-in';
    }

    /**
     * Get all pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Get all completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }
} 