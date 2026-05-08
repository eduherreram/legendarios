<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
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
            'numero_legendario' => null,
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'rut' => fake()->unique()->numerify('########-#'),
            'fecha_nacimiento' => fake()->date(),
            'enfermedad' => false,
            'talla' => fake()->randomElement(['S', 'M', 'L', 'XL', 'XXL']),
            'iglesia' => fake()->optional()->company(),
            'es_pastor' => false,
            'direccion_calle' => fake()->streetName(),
            'direccion_numero' => fake()->buildingNumber(),
            'direccion_comuna' => fake()->city(),
            'direccion_region' => fake()->state(),
            'direccion_pais' => fake()->country(),
            'telefono' => fake()->phoneNumber(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'nombre_contacto_emergencia' => fake()->name(),
            'parentesco_contacto_emergencia' => fake()->randomElement(['Padre', 'Madre', 'Hermano', 'Hermana', 'Esposo', 'Esposa', 'Hijo', 'Hija', 'Amigo']),
            'telefono_contacto_emergencia' => fake()->phoneNumber(),
            'estado' => 'activo',
            'fecha_inscripcion' => now(),
            'departamento_id' => null,
            'remember_token' => Str::random(10),
        ];
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
