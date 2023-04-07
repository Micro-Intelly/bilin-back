<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    #[ArrayShape(['name' => "string"])]
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word()
        ];
    }
    /**
     * Method to create tag with specific name
     * @param string $name
     * @return TagFactory
     */
    public function withName(string $name): TagFactory
    {
        return $this->state(fn (array $attributes) => [
            'name' => $name,
        ]);
    }

}
