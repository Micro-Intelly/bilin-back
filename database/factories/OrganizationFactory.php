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

    /**
     * Method to create organization with specific name
     * @param string $name
     * @return OrganizationFactory
     */
    public function withName(string $name): OrganizationFactory
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }
}
