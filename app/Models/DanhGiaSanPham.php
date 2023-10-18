<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DanhGiaSanPham extends Model
{
    use HasFactory;
    protected $table = 'danh_gia_san_phams';
    protected $fillable = [
    'id_san_pham',
    'sao_danh_gia',
    'binh_luan_danh_gia',
    'id_nguoi_dung'
    ];

    public function nguoiDanhGia()
    {
        return $this->belongsTo(NguoiDung::class, 'id_nguoi_dung');
    }
}
