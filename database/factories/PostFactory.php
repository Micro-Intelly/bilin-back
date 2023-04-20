<?php

namespace Database\Factories;

use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    #[ArrayShape(['title' => "string", 'body' => "string", 'user_id' => "\Database\Factories\UserFactory", 'language_id' => "\Database\Factories\LanguageFactory"])]
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'body' => '<p>'.fake()->text(1000).'</p>',
            'user_id' => User::factory(),
            'language_id' => Language::factory(),
        ];
    }
    /**
     * Create comment with specific author.
     * @param User $user
     * @return PostFactory
     */
    public function withUser(User $user): PostFactory
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user,
        ]);
    }
    /**
     * Create comment with specific language.
     * @param Language $language
     * @return PostFactory
     */
    public function withLanguage(Language $language): PostFactory
    {
        return $this->state(fn (array $attributes) => [
            'language_id' => $language,
        ]);
    }
}
