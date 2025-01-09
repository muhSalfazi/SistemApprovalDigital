<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    use HasFactory;

    protected $table = 'tbl_departement';

    protected $fillable = [
        'nama_departement',
    ];

    public function Departement()
    {
        return $this->hasMany(Departement::class, 'id_departement');
    }

}
