<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietHoaDon extends Model
{
    use HasFactory;
    protected $table = 'chi_tiet_hoa_dons';
    protected $fillable = [
        'id_hoa_don',
        'id_san_pham',
        'gia_tien',
        'so_luong',
        'id_danh_gia'
    ];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'id_san_pham');
    }

    public function danhGia()
    {
        return $this->belongsTo(DanhGiaSanPham::class, 'id_danh_gia');
    }

    public function hoaDon()
    {
        return $this->belongsTo(HoaDon::class, 'id_hoa_don');
    }
}
