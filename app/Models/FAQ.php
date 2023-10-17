<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    use HasFactory;
    protected $table = 'f_a_q_s';
    protected $fillable = [
    'id_chu_de',
    'cau_hoi',
    'cau_tra_loi'
    ];
    public function chuDe()
    {
        return $this->belongsTo(ChuDeFAQ::class, 'id_chu_de');
    }
}
