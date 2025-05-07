<?php

namespace App\Models;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Table extends Model
{
    use HasFactory;
    protected $table = 'table';
    protected $primaryKey = 'TableID';

    protected $fillable = [
        'TableNumber',
        'Seats',
        'Location',
        'Status'
    ];

    /**
     * Quan hệ với Reservation.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'TableID');
    }
    public function activeReservations()
    {
        return $this->hasMany(Reservation::class, 'TableID')
            ->where('Status', 'Đã xác nhận');
    }
}
