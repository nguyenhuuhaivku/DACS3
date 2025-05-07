<?php

namespace App\Models\user;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;
    protected $table = 'cart'; 
    protected $fillable = ['user_id', 'item_id', 'quantity', 'ReservationID'];

    // Liên kết với bảng users
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Liên kết với bảng menuitem
    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class, 'item_id', 'ItemID');
    }
}

?>
