<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Serie;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Test>
 */
class TestFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    #[ArrayShape(['name' => "array|string", 'description' => "string", 'series_id' => "\Database\Factories\SerieFactory", 'language_id' => "\Database\Factories\LanguageFactory", 'user_id' => "\Database\Factories\UserFactory"])]
    public function definition(): array
    {
        return [
            'name' => fake()->words(rand(2, 7), true),
            'description' => fake()->paragraph(),
            'series_id' => Serie::factory(),
            'language_id' => Language::factory(),
            'user_id' => User::factory()
        ];
    }

    /**
     * Create comment with specific author.
     * @param Serie $series
     * @return TestFactory
     */
    public function withSeries(Serie $series): TestFactory
    {
        return $this->state(fn (array $attributes) => [
            'series_id' => $series,
        ]);
    }
    /**
     * Create comment with specific language.
     * @param Language $language
     * @return TestFactory
     */
    public function withLanguage(Language $language): TestFactory
    {
        return $this->state(fn (array $attributes) => [
            'language_id' => $language,
        ]);
    }
    /**
     * Create comment with specific author.
     * @param User $user
     * @return TestFactory
     */
    public function withUser(User $user): TestFactory
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user,
        ]);
    }
}
