<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * 
 *
 * @property int $id
 * @property string $nama_kategori
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submission
 * @property-read int|null $submission_count
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori query()
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori whereNamaKategori($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Kategori whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Kategori extends Model
{
    use HasFactory;

    protected $table = 'tbl_kategori';

    protected $fillable = [
        'nama_kategori',
    ];

    public function submission()
    {
        return $this->hasMany(Submission::class, 'id_kategori');
    }



}
