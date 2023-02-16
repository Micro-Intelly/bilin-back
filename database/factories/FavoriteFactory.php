<?php

namespace Database\Factories;

use App\Models\Episode;
use App\Models\Post;
use App\Models\Serie;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Favorite>
 */
class FavoriteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['user_id' => "\Database\Factories\UserFactory", 'favorite_able_id' => "mixed", 'favorite_able_type' => "mixed"])]
    public function definition(): array
    {
        $favorite_able = $this->favorite_able();
        return [
            'user_id' => User::factory(),
            'favorite_able_id' => $favorite_able::factory(),
            'favorite_able_type' => $favorite_able
        ];
    }

    /**
     * Return random favorite class.
     */
    public function favorite_able()
    {
        return $this->faker->randomElement([
            Post::class,
            Serie::class,
            Test::class
        ]);
    }

    /**
     * Create comment with specific favorite able.
     * @param mixed $favorite_able
     * @return FavoriteFactory
     */
    public function withFavoriteAble(mixed $favorite_able): FavoriteFactory
    {
        return $this->state(fn (array $attributes) => [
            'favorite_able_id' => $favorite_able,
            'favorite_able_type' => class_basename($favorite_able),
        ]);
    }
    /**
     * Create comment with specific author.
     * @param User $user
     * @return FavoriteFactory
     */
    public function withUser(User $user): FavoriteFactory
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user,
        ]);
    }
}
