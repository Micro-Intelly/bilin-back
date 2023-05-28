<?php

namespace Database\Factories;

use App\Models\Section;
use App\Models\Serie;
use App\Models\User;
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
    #[ArrayShape(['title' => "array|string", 'description' => "string", 'path' => "mixed", 'type' => "mixed", 'section_id' => "\Database\Factories\SectionFactory", 'user_id' => "\Database\Factories\UserFactory", 'serie_id' => "\Database\Factories\SerieFactory"])]
    public function definition(): array
    {
        return [
            'title' => fake()->words(rand(2, 7), true),
            'description' => fake()->paragraph(),
            'path' => fake()->randomElement(['app/videos/file_example_MP4_1920_18MG.mp4', 'app/podcasts/Free_Test_Data_10MB_MP3.mp3']),
            'type' => fake()->randomElement(['video', 'podcast']),
            'section_id' => Section::factory(),
            'user_id' => User::factory(),
            'serie_id' => Serie::factory()
        ];
    }

    /**
     * Create comment with specific section.
     * @param Section $section
     * @return EpisodeFactory
     */
    public function withSection(Section $section): EpisodeFactory
    {
        $type = $section->serie->type;
        $path = fake()->randomElement([
            'app/videos/file_example_MP4_1920_18MG.mp4',
            'app/videos/file_example_MP4_480_1_5MG.mp4',
            'app/videos/sample-20s.mp4',
            'app/videos/sample-30s.mp4',
        ]);
        if($type != 'video'){$path = 'app/podcasts/Free_Test_Data_10MB_MP3.mp3';}
        return $this->state(fn (array $attributes) => [
            'section_id' => $section,
            'type' => $type,
            'path' => $path,
            'user_id' => $section->serie->author,
            'serie_id' => $section->serie->id
        ]);
    }
}
