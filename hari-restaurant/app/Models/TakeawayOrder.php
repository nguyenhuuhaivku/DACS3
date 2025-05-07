<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TakeawayOrder extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'takeaway_orders';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_code',
        'user_id',
        'customer_name',
        'phone',
        'address',
        'total_amount',
        'status',
        'payment_method',
        'payment_status',
        'note',
        'delivery_time',
        'estimated_delivery_time'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_amount' => 'decimal:2',
        'delivery_time' => 'datetime',
        'estimated_delivery_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
        return $this->hasMany(TakeawayOrderItem::class, 'order_id');
    }

    /**
     * Get the tracking events for the order.
     */
    public function tracking(): HasMany
    {
        return $this->hasMany(TakeawayOrderTracking::class, 'order_id');
    }

    /**
     * Get all pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Get all confirmed orders.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'Confirmed');
    }

    /**
     * Get all in preparation orders.
     */
    public function scopeInPreparation($query)
    {
        return $query->where('status', 'In Preparation');
    }

    /**
     * Get all out for delivery orders.
     */
    public function scopeOutForDelivery($query)
    {
        return $query->where('status', 'Out for Delivery');
    }

    /**
     * Get all delivered orders.
     */
    public function scopeDelivered($query)
    {
        return $query->where('status', 'Delivered');
    }

    /**
     * Get all cancelled orders.
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'Cancelled');
    }

    /**
     * Get all current orders (not delivered or cancelled).
     */
    public function scopeCurrent($query)
    {
        return $query->whereNotIn('status', ['Delivered', 'Cancelled']);
    }

    /**
     * Get all completed orders (delivered or cancelled).
     */
    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['Delivered', 'Cancelled']);
    }
} 