<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrangThaiHoaDon extends Model
{
    use HasFactory;
    protected $table = 'trang_thai_hoa_dons';
    protected $fillable = [
        'id_hoa_don',
        'id_trang_thai',
        'id_nhan_vien',
        'ngay_cap_nhap',
        'ghi_chu',
    ];
    public function trangThai()
    {
        return $this->belongsTo(TrangThai::class, 'id_trang_thai');
    }

    public function hoaDon()
    {
        return $this->belongsTo(HoaDon::class, 'id_hoa_don');
    }

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'id_nhan_vien');
    }
}
