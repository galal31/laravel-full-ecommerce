<?php

namespace Database\Factories;

use App\Models\Dashboard\City; // تأكد من استدعاء الموديل الصحيح الخاص بمسارك
use App\Models\Dashboard\Governorate;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        $city = City::inRandomOrder()->first();

        $governorateId = $city ? $city->governorate_id : null;
        $countryId = $governorateId ? Governorate::find($governorateId)->country_id : null;

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'is_active' => 1,
            
            'country_id' => $countryId,
            'governorate_id' => $governorateId,
            'city_id' => $city ? $city->id : null,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}