<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\Organization;
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
    #[ArrayShape(['title' => "array|string", 'description' => "string", 'level' => "mixed", 'access' => "mixed", 'series_id' => "null", 'language_id' => "\Database\Factories\LanguageFactory", 'user_id' => "\Database\Factories\UserFactory", 'organization_id' => "null"])]
    public function definition(): array
    {
        return [
            'title' => fake()->words(rand(2, 7), true),
            'description' => fake()->paragraph(),
            'level' => fake()->randomElement(['basic', 'intermediate','advanced']),
            'access' => fake()->randomElement(['public', 'registered']),
            'series_id' => null,
            'language_id' => Language::factory(),
            'user_id' => User::factory(),
            'organization_id' => null
        ];
    }

    /**
     * Create test with specific series.
     * @param Serie $series
     * @return TestFactory
     */
    public function withSeries(Serie $series): TestFactory
    {
        return $this->state(fn (array $attributes) => [
            'series_id' => $series,
            'level' => $series->level,
            'access' => $series->access,
            'organization_id' => $series->organization_id
        ]);
    }
    /**
     * Create test with specific language.
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
     * Create test with specific author.
     * @param User $user
     * @return TestFactory
     */
    public function withUser(User $user): TestFactory
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user,
        ]);
    }
    /**
     * Create test with specific organization.
     * @param Organization $org
     * @return TestFactory
     */
    public function withOrg(Organization $org): TestFactory
    {
        return $this->state(fn (array $attributes) => [
            'access' => 'org',
            'organization_id' => $org,
        ]);
    }
}
