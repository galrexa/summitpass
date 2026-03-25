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
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name'              => fake()->name(),
            'email'             => fake()->unique()->safeEmail(),
            'phone'             => fake()->phoneNumber(),
            'nik'               => fake()->unique()->numerify('################'), // 16 digit
            'passport_number'   => null,
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'role'              => 'pendaki',
            'remember_token'    => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function pengelolaTn(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'pengelola_tn',
            'nik'  => null,
        ]);
    }

    public function officer(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'officer',
            'nik'  => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'admin',
            'nik'  => null,
        ]);
    }

    public function wna(): static
    {
        return $this->state(fn (array $attributes) => [
            'nik'             => null,
            'passport_number' => strtoupper(fake()->bothify('??#######')),
        ]);
    }
}
