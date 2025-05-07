<?php


namespace App\Models;


use App\Models\user\Cart;
use App\Models\user\MenuItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Reservation extends Model
{
    use HasFactory;


    protected $table = 'reservation';
    protected $primaryKey = 'ReservationID';


    const CREATED_AT = 'CreatedAt';
    const UPDATED_AT = 'UpdatedAt';


    protected $fillable = [
        'ReservationCode',
        'UserID',
        'FullName',
        'Phone',
        'TableID',
        'GuestCount',
        'ReservationDate',
        'Status',
        'Note',
        'CheckInTime',
        'CheckOutTime'
    ];


    protected $dates = [
        'ReservationDate',
        'CheckInTime',
        'CheckOutTime',
        'CreatedAt',
        'UpdatedAt'
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'id');
    }


    public function table()
    {
        return $this->belongsTo(Table::class, 'TableID', 'TableID');
    }


    public function reservationItems()
    {
        return $this->hasMany(ReservationItem::class, 'ReservationID', 'ReservationID');
    }


    public function cartItems()
    {
        return $this->hasMany(Cart::class, 'ReservationID', 'ReservationID');
    }


    public function menuItems()
    {
        return $this->hasManyThrough(
            MenuItem::class,
            Cart::class,
            'ReservationID', // Khóa ngoại trên bảng cart
            'ItemID', // Khóa chính trên bảng menuitem
            'ReservationID', // Khóa chính trên bảng reservation
            'item_id' // Khóa ngoại trên bảng cart tham chiếu đến menuitem
        );
    }


    public function payment()
    {
        return $this->hasOne(Payment::class, 'ReservationID', 'ReservationID');
    }


    public function setCheckInTimeAttribute($value)
    {
        $this->attributes['CheckInTime'] = $value ? \Carbon\Carbon::parse($value) : null;
    }


    public function setCheckOutTimeAttribute($value)
    {
        $this->attributes['CheckOutTime'] = $value ? \Carbon\Carbon::parse($value) : null;
    }
}
