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
        return $this->belongsTo(LoaiSanPham::class,'loai_san_phams.id','san_pham.id_loai_san_pham');
    }
}
