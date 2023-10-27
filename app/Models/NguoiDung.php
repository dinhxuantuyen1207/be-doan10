<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NguoiDung extends Model
{
    use HasFactory;
    protected $table = 'nguoi_dungs';
    protected $fillable = [
        'tai_khoan',
        'mat_khau',
        'ten_nguoi_dung',
        'dia_chi',
        'so_dien_thoai',
        'email',
        'anh_dai_dien'
    ];
}
