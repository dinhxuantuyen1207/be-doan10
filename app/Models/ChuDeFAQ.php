<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChuDeFAQ extends Model
{
    use HasFactory;
    protected $table = 'chu_de_f_a_q_s';
    protected $fillable = [
    'ten_chu_de',
    ];
    public function fAQ()
    {
        return $this->hasMany(FAQ::class, 'id_chu_de');
    }
}
