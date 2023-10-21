<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoaiSanPham extends Model
{
    use HasFactory;
    protected $table = 'loai_san_phams';
    protected $fillable = [
        'ten_loai_san_pham',
        'id_dong_san_pham'
    ];
    public function dongSanPham()
    {
        return $this->belongsTo(DongSanPham::class, 'id_dong_san_pham');
    }
}
