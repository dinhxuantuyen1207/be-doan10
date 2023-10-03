<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DongSanPham extends Model
{
    use HasFactory;
    protected $table = 'dong_san_phams';
    protected $fillable = [
        'ten_dong_san_pham'
    ];
}
