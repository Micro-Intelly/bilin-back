<?php

namespace Database\Factories;

use App\Models\Serie;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    #[ArrayShape(['name' => "array|string", 'description' => "string", 'path' => "string", 'series_id' => "\Illuminate\Database\Eloquent\Factories\Factory"])]
    public function definition(): array
    {
        return [
            'name' => fake()->words(rand(2, 7), true),
            'description' => fake()->paragraph(),
            'path' => '/app/file/dummy.pdf',
            'series_id' => Serie::factory()
        ];
    }
    /**
     * Create comment with specific author.
     * @param Serie $series
     * @return FileFactory
     */
    public function withSeries(Serie $series): FileFactory
    {
        return $this->state(fn (array $attributes) => [
            'series_id' => $series,
        ]);
    }
}
