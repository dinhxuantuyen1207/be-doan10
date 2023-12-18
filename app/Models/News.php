<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $table = 'news';
    protected $fillable = [
        'tieu_de_1',
        'tieu_de_2',
        'mo_ta',
        'anh_tin_tuc',
        'tin_tuc'
    ];
}
