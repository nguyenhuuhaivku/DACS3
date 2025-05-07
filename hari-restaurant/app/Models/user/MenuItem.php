<?php

namespace App\Models\user;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;

    protected $table = 'menuitem';
    protected $primaryKey = 'ItemID';
    protected $fillable = [
        'CategoryID',
        'ItemName',
        'Price',
        'Description',
        'Available',
        'ImageURL',
        'Status',
    ];
    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'CategoryID');
    }
    public function carts()
    {
        return $this->hasMany(Cart::class, 'item_id', 'ItemID');
    }
}
