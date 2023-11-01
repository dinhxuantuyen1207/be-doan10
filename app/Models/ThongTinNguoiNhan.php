<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongTinNguoiNhan extends Model
{
    use HasFactory;
    protected $table = 'thong_tin_nguoi_nhans';
    protected $fillable = [
        'id_hoa_don',
        'ten_nguoi_nhan',
        'so_dien_thoai',
        'dia_chi'
    ];
}
