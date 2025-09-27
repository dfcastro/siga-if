<?php

namespace App\Models;

// Importamos as classes auxiliares
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Define um "mutator" para o atributo 'license_plate'.
     * Converte a placa para maiúsculas antes de salvar.
     */
    protected function licensePlate(): Attribute
    {
        return Attribute::make(
            // Str::upper() converte a string inteira para MAIÚSCULAS.
            set: fn(string $value) => Str::upper($value),
        );
    }

    //  Define a relação com as Entradas Particulares.
    //  * Um veículo pode ter muitas entradas particulares.
    //  */
    public function privateEntries()
    {
        return $this->hasMany(PrivateEntry::class);
    }

    public function officialTrips()
    {
        return $this->hasMany(OfficialTrip::class);
    }
}
