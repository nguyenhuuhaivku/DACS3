<?php

namespace App\Models\user;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use HasFactory;

    protected $table = 'menucategory';
    protected $primaryKey = 'CategoryID';

    public function menuItems()
    {
        return $this->hasMany(MenuItem::class, 'CategoryID');
    }
}
