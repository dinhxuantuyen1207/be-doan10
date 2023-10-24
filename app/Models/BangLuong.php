<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BangLuong extends Model
{
    use HasFactory;
    protected $table = 'bang_luongs';
    protected $fillable = [
        'id_nhan_vien',
        'thang_nam',
        'cham_cong',
        'he_so',
        'thuong',
    ];
}
