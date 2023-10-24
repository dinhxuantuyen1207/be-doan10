<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuyenHan extends Model
{
    use HasFactory;
    protected $table = 'quyen_hans';
    protected $fillable = [
        'ten_quyen_han',
        'id_master'
    ];
}
