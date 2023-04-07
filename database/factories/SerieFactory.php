<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Serie>
 */
class SerieFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    #[ArrayShape(['title' => "array|string", 'description' => "string", 'author_id' => "\Database\Factories\UserFactory", 'organization_id' => "null", 'language_id' => "\Database\Factories\LanguageFactory", 'type' => "mixed", 'level' => "mixed", 'access' => "mixed"])]
    public function definition(): array
    {
        return [
            'title' => fake()->words(rand(3, 10), true),
            'description' => fake()->text(),
            'author_id' => User::factory(),
            'organization_id' => null,
            'language_id' => Language::factory(),
            'type' => fake()->randomElement(['video', 'podcast']),
            'level' => fake()->randomElement(['basic', 'intermediate','advanced']),
            'access' => fake()->randomElement(['public', 'registered']),
        ];
    }
    /**
     * Create series with specific author.
     * @param User $user
     * @return SerieFactory
     */
    public function withUser(User $user): SerieFactory
    {
        return $this->state(fn (array $attributes) => [
            'author_id' => $user,
        ]);
    }
    /**
     * Create series with specific organization.
     * @param Organization $org
     * @return SerieFactory
     */
    public function withOrg(Organization $org): SerieFactory
    {
        return $this->state(fn (array $attributes) => [
            'access' => 'org',
            'organization_id' => $org,
        ]);
    }
    /**
     * Create series with specific language.
     * @param Language $language
     * @return SerieFactory
     */
    public function withLanguage(Language $language): SerieFactory
    {
        return $this->state(fn (array $attributes) => [
            'language_id' => $language,
        ]);
    }
}
