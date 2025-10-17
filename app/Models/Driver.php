<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    // ADICIONE ESTA LINHA
    protected $guarded = [];

    // Adicione esta propriedade
    protected $casts = [
        'is_authorized' => 'boolean',
    ];
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    /**
     * Define um "mutator" para o atributo 'name'.
     * Este código será executado automaticamente sempre que o nome for salvo.
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            // A função 'set' é o nosso "porteiro". Ela recebe o valor e o modifica.
            // Str::title() é uma função do Laravel que coloca a primeira letra de cada palavra em maiúsculo.
            set: fn(string $value) => Str::title($value),
        );
    }
    public function privateEntries()
    {
        return $this->hasMany(PrivateEntry::class);
    }

    /**
     * Define a relação com as Viagens Oficiais.
     * Um condutor pode ter muitas viagens oficiais.
     */
    public function officialTrips()
    {
        return $this->hasMany(OfficialTrip::class);
    }
}
