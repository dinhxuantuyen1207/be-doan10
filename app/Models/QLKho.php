<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QLKho extends Model
{
    use HasFactory;
    protected $table = 'q_l_khos';
    protected $fillable = [
        'id_san_pham',
        'so_luong_nhap',
        'so_luong_da_ban'
    ];

    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'id_san_pham');
    }
}
