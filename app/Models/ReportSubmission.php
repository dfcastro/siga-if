<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportSubmission extends Model
{
    use HasFactory;

    // Seus campos fillable
    protected $fillable = [
        'guard_id',
        'fiscal_id',
        'assigned_fiscal_id',
        'vehicle_id',
        'start_date',
        'end_date',
        'observation',
        'type',
        'status',
        'submitted_at',
        'approved_at',
    ];

    // Seus casts de data
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    // Seus relacionamentos existentes
    public function guardUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'guard_id');
    }

    public function fiscal(): BelongsTo
    {
        return $this->belongsTo(User::class, 'fiscal_id');
    }

    /**
     * CORREÇÃO APLICADA AQUI
     *
     * Define a relação com o Veículo.
     * Uma submissão de relatório (especificamente de veículo oficial) pertence a um veículo.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
