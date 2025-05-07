<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuCategory extends Model
{
    use HasFactory;

    protected $table = 'menucategory';

    protected $primaryKey = 'CategoryID';

    protected $fillable = ['CategoryName', 'Description'];

    public $timestamps = false; 
}
