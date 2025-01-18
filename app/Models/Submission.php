<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $id_departement
 * @property int $id_kategori
 * @property int $id_user
 * @property string $title
 * @property string $no_transaksi
 * @property string $remark
 * @property string $lampiran_pdf
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Approval> $approvals
 * @property-read int|null $approvals_count
 * @property-read \App\Models\Departement $departement
 * @property-read \App\Models\Kategori $kategori
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission query()
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereIdDepartement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereIdKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereIdUser($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereLampiranPdf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereNoTransaksi($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Submission whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
    public function departement()
    {
        return $this->belongsTo(Departement::class, 'id_departement');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'id_submission');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

}
