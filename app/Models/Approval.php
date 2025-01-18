<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 *
 *
 * @property int $id
 * @property int $id_submission
 * @property int $auditor_id
 * @property string $status
 * @property string $approved_date
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Submission $submission
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Approval newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval query()
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereApprovedDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereAuditorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereIdSubmission($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Approval whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Approval extends Model
{
    use HasFactory;

    protected $table = 'tbl_approval';

    protected $fillable = [
        'id_submission',
        'auditor_id',
        'status',
        'approved_date',
        'remark',
    ];

    protected $casts = [
        'approved_date' => 'datetime',
    ];


    public function submission()
    {
        return $this->belongsTo(Submission::class, 'id_submission');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }


}
