<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TakeawayOrderTracking extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'takeaway_order_tracking';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'order_id',
        'status',
        'comment',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order that owns the tracking record.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(TakeawayOrder::class, 'order_id');
    }

    /**
     * Get the admin user who created the tracking record.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
} 