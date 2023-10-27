<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    use HasFactory;
    protected $table = 'thong_baos';
    protected $fillable = [
        'id_nguoi_dung',
        'thong_bao',
        'ngay_thong_bao',
        'trang_thai_thong_bao',
    ];
}
