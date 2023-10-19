<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrangThai extends Model
{
    use HasFactory;
    protected $table = 'trang_thais';
    protected $fillable = [
        'trang_thai'
    ];
}
