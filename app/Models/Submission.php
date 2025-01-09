<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;

    // table name
    protected $table = 'tbl_submission';

    protected $fillable = [
        'id_departement',
        'id_kategori',
        'id_user',
        'title',
        'no_transaksi',
        'remark',
        'lampiran_pdf',
    ];

    // foreign key
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }
    public function Departement()
    {
        return $this->belongsTo(Departement::class, 'id_departement');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    // end foreign key
}
