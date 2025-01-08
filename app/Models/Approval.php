<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $table = 'tbl_approval';

    protected $fillable = [
        'id_submission',
        'auditor_id',
        'status',
        'approved_date'
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
