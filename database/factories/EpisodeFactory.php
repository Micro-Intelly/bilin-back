<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\Serie;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Episode>
 */
class EpisodeFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    #[ArrayShape(['name' => "array|string", 'description' => "string", 'path' => "mixed", 'section_id' => "\Illuminate\Database\Eloquent\Factories\Factory"])]
    public function definition(): array
    {
        return [
            'name' => fake()->words(rand(2, 7), true),
            'description' => fake()->paragraph(),
            'path' => fake()->randomElement(['/app/videos/file_example_MP4_1920_18MG.mp4', '/app/podcasts/Free_Test_Data_10MB_MP3.mp3']),
            'section_id' => Section::factory()
        ];
    }

    /**
     * Create comment with specific author.
     * @param Section $section
     * @return EpisodeFactory
     */
    public function withSections(Section $section): EpisodeFactory
    {
        return $this->state(fn (array $attributes) => [
            'section_id' => $section,
        ]);
    }
}
