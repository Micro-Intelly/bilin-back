<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Organization>
 */
class OrganizationFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    #[ArrayShape(['name' => "array|string", 'description' => "string"])]
    public function definition(): array
    {
        return [
            'name' => fake()->words(rand(2, 4), true),
            'description' => fake()->paragraph(),
        ];
    }
}
