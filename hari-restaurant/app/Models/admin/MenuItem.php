<?php

namespace App\Models\admin;

use App\Models\admin\MenuCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class MenuItem extends Model
{
    use HasFactory;

    protected $table = 'menuitem';
    protected $primaryKey = 'ItemID';


    protected $fillable = [
        'CategoryID',
        'ItemName',
        'Price',
        'status',
        'Description',
        'ImageURL',
        'Available',
    ];

    public $timestamps = false;

    // Quan hệ với bảng menucategory
    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'CategoryID', 'CategoryID');
    }

    // Thêm các constant để dễ quản lý
    const STATUS_NEW = 'Món mới';
    const STATUS_POPULAR = 'Phổ biến';
    const STATUS_FEATURED = 'Đặc biệt';
    const STATUS_NORMAL = 'Bình thường';

    public static function getStatusOptions()
    {
        return [
            self::STATUS_NORMAL => 'Bình thường',
            self::STATUS_NEW => 'Món mới',
            self::STATUS_POPULAR => 'Phổ biến',
            self::STATUS_FEATURED => 'Đặc biệt'
        ];
    }

    public function getStatusBadgeClassAttribute()
    {
        return [
            self::STATUS_NEW => 'bg-success',
            self::STATUS_POPULAR => 'bg-primary',
            self::STATUS_FEATURED => 'bg-warning',
            self::STATUS_NORMAL => 'bg-secondary'
        ][$this->status] ?? 'bg-secondary';
    }
}
