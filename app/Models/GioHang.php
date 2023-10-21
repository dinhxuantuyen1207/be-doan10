<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    use HasFactory;
    protected $table = 'gio_hangs';
    protected $fillable = [
        'id_nguoi_dung'
    ];

    public function chiTietGioHang()
    {
        return $this->hasMany(ChiTietGioHang::class, 'id_gio_hang');
    }
}
