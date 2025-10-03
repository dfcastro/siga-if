<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'guard_id',
        'start_date',
        'end_date',
        'submitted_at',
        'fiscal_id',
        'approved_at',
        'status',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Relação para saber qual porteiro (User) submeteu.
     * O nome foi alterado de guard() para guardUser() para evitar conflito.
     */
    public function guardUser()
    {
        return $this->belongsTo(User::class, 'guard_id');
    }

    /**
     * Relação para saber qual fiscal (User) aprovou.
     */
    public function fiscal()
    {
        return $this->belongsTo(User::class, 'fiscal_id');
    }
}
