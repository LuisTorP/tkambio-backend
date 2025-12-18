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
        $year = fake()->numberBetween(1950, 2010);
        $month = fake()->numberBetween(1, 12);
        $day = fake()->numberBetween(1, 28);
        
        $dateInit = sprintf('%04d-%02d-%02d', $year, $month, $day);
        
        $yearsAfter = fake()->numberBetween(1, 5);
        $dateEnd = date('Y-m-d', strtotime($dateInit . " +{$yearsAfter} years"));

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'date_birth_init' => $dateInit,
            'date_birth_end' => $dateEnd,
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
