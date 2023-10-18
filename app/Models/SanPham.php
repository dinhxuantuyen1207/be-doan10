<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    use HasFactory;
    protected $table = 'san_phams';
    protected $fillable = [
        'ten_san_pham',
        'gia',
        'id_loai_san_pham',
        'mo_ta_ngan',
        'mo_ta',
        'khuyen_mai',
    ];
    public function loaiSanPham()
    {
        return $this->belongsTo(LoaiSanPham::class, 'id_loai_san_pham');
    }
    public function hinhAnh()
    {
        return $this->hasMany(HinhAnhSanPham::class, 'id_san_pham');
    }

    public function danhGia()
    {
        return $this->hasMany(DanhGiaSanPham::class, 'id_san_pham');
    }
}
