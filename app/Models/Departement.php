<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 *
 *
 * @property int $id
 * @property string $nama_departement
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Submission> $submission
 * @property-read int|null $submission_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $user
 * @property-read int|null $user_count
 * @method static \Illuminate\Database\Eloquent\Builder|Departement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Departement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Departement query()
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereNamaDepartement($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Departement whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Departement extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'tbl_departement';

    protected $fillable = [
        'id',
        'nama_departement',
        'deksripsi'
    ];

    // buat softdelate di departement
    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->hasMany(User::class, 'id_departement');
    }
    public function submission()
    {
        return $this->hasMany(Submission::class, 'id_departement');
    }
}
