<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrivateEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'vehicle_id',
        'license_plate',
        'vehicle_model',
        'entry_reason',
        'entry_at',
        'exit_at',
        'guard_on_entry',
        'guard_on_exit',
    ];

    /**
     * Define which attributes should be cast to native types.
     *
     * @var array
     */
    // ADICIONE ESTA PROPRIEDADE
    protected $casts = [
        'entry_at' => 'datetime',
        'exit_at' => 'datetime',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class)->withTrashed();
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
