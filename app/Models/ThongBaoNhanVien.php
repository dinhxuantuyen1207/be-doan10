<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBaoNhanVien extends Model
{
    use HasFactory;
    protected $table = 'thong_bao_nhan_viens';
    protected $fillable = [
        'id_nhan_vien',
        'thong_bao',
        'ngay_thong_bao',
        'trang_thai_thong_bao',
    ];
}
