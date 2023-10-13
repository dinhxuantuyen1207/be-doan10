<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HinhAnhSanPham extends Model
{
    use HasFactory;
    protected $table = 'hinh_anh_san_phams';
    protected $fillable = [
        'id_san_pham',
        'hinh_anh_san_pham'
    ];
}
