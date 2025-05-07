<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TakeawayOrderItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'takeaway_order_items';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'item_id',
        'quantity',
        'price',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
    ];

    /**
     * Get the order that owns the item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(TakeawayOrder::class, 'order_id');
    }

    /**
     * Get the menu item associated with this order item.
     */
    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class, 'item_id');
    }

    /**
     * Get the subtotal for this item.
     *
     * @return float
     */
    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->quantity;
    }
} 