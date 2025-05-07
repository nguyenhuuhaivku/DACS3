<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payment';
    protected $primaryKey = 'PaymentID';

    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = 'UpdatedAt';

    protected $fillable = [
        'PaymentCode',
        'ReservationID',
        'Amount',
        'PaymentMethod',
        'Status'
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'ReservationID', 'ReservationID');
    }
}
