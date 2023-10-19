<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HoaDon extends Model
{
    use HasFactory;
    protected $table = 'hoa_dons';
    protected $fillable = [
        'id_nguoi_dung',
        'ngay_mua',
        'gia_tien_thanh_toan',
        'trang_thai_thanh_toan',
        'ngay_thanh_toan',
        'id_trang_thai'
    ];
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_dung');
    }

    public function trangThai()
    {
        return $this->belongsTo(TrangThai::class, 'id_trang_thai');
    }
    public function trangThaiHoaDon()
    {
        return $this->hasMany(TrangThaiHoaDon::class, 'id_hoa_don');
    }
}
