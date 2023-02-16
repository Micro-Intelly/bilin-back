<?php

namespace Database\Factories;

use App\Models\Serie;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Section>
 */
class SectionFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    #[ArrayShape(['name' => "array|string", 'description' => "string", 'series_id' => "\Illuminate\Database\Eloquent\Factories\Factory"])]
    public function definition(): array
    {
        return [
            'name' => fake()->words(rand(2, 7), true),
            'description' => fake()->paragraph(),
            'series_id' => Serie::factory()
        ];
    }

    /**
     * Create comment with specific series.
     * @param Serie $series
     * @return SectionFactory
     */
    public function withSeries(Serie $series): SectionFactory
    {
        return $this->state(fn (array $attributes) => [
            'series_id' => $series,
        ]);
    }
}
