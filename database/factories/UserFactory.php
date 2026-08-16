<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * A password de teste padrão.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'porteiro', // Define 'porteiro' como o padrão
            'fiscal_type' => null,
        ];
    }

    /**
     * Indica que o utilizador é um admin.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
        ]);
    }

    /**
     * Indica que o utilizador é um porteiro.
     */
    public function porteiro(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'porteiro',
        ]);
    }

    /**
     * Indica que o utilizador é um fiscal.
     */
    public function fiscal(string $type = 'both'): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'fiscal',
            'fiscal_type' => $type, // 'private', 'official', ou 'both'
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}