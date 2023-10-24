<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhanVien extends Model
{
    use HasFactory;
    protected $table = 'nhan_viens';
    protected $fillable = [
        'tai_khoan',
        'mat_khau',
        'ten_nhan_vien',
        'so_dien_thoai',
        'id_chuc_vu',
        'luong_co_ban',
        'anh_nhan_vien',
        'anh_cccd',
    ];

    public function chucVu()
    {
        return $this->belongsTo(ChucVu::class, 'id_chuc_vu');
    }
}
