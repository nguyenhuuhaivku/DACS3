<?php

namespace App\Models;

use App\Models\user\MenuItem;
use Illuminate\Database\Eloquent\Model;

class ReservationItem extends Model
{
    protected $table = 'reservation_item';
    protected $primaryKey = 'ReservationItemID';
    public $timestamps = false;

    protected $fillable = [
        'ReservationID',
        'ItemID',
        'Quantity',
        'Price'
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'ItemID', 'ItemID');
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'ReservationID', 'ReservationID');
    }
}
