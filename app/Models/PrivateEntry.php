<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateEntry extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'driver_id',
        'vehicle_id', // Adicionar se não estava (vi que usou no CreatePrivateEntry)
        'license_plate',
        'vehicle_model',
        'entry_reason',
        'entry_at',
        'exit_at',
        // 'guard_on_entry', // <-- REMOVER (ou comentar)
        // 'guard_on_exit',  // <-- REMOVER (ou comentar)
        'guard_on_entry_id', // <-- ADICIONAR
        'guard_on_exit_id',  // <-- ADICIONAR
        'report_submission_id' // Adicionar se ainda não estiver
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'entry_at' => 'datetime',
        'exit_at' => 'datetime',
    ];

    /**
     * Get the driver associated with the entry.
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class)->withTrashed();
    }

    /**
     * Get the vehicle associated with the entry (if applicable).
     * Pode ser null se a placa/modelo foram digitados manualmente.
     */
    public function vehicle(): BelongsTo
    {
        // Adicionar withTrashed() se quiser incluir veículos excluídos
        return $this->belongsTo(Vehicle::class)->withTrashed();
    }

    /**
     * Get the user (guard) who registered the entry.
     */
    public function guardEntry(): BelongsTo // <-- ADICIONAR RELAÇÃO
    {
        return $this->belongsTo(User::class, 'guard_on_entry_id');
    }

    /**
     * Get the user (guard) who registered the exit.
     */
    public function guardExit(): BelongsTo // <-- ADICIONAR RELAÇÃO
    {
        return $this->belongsTo(User::class, 'guard_on_exit_id');
    }

     /**
     * Get the report submission this entry belongs to.
     */
     public function reportSubmission(): BelongsTo // <-- ADICIONAR RELAÇÃO (se ainda não existir)
     {
         return $this->belongsTo(ReportSubmission::class);
     }
}