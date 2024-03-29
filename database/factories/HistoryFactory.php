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
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\History>
 */
class HistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['user_id' => "\Database\Factories\UserFactory", 'serie_id' => "null", 'history_able_id' => "mixed", 'history_able_type' => "mixed"])]
    public function definition(): array
    {
        $history_able = $this->history_able();
        return [
            'user_id' => User::factory(),
            'serie_id' => null,
            'history_able_id' => $history_able::factory(),
            'history_able_type' => $history_able
        ];
    }

    /**
     * Return random history class.
     */
    public function history_able()
    {
        return $this->faker->randomElement([
            Post::class,
            Episode::class,
            Test::class
        ]);
    }

    /**
     * Create comment with specific history able.
     * @param mixed $history_able
     * @return HistoryFactory
     */
    public function withHistoryAble(mixed $history_able): HistoryFactory
    {
        $serie_id = null;
        if(str_ends_with(get_class($history_able), "Episode")){
            $serie_id = $history_able->serie_id;
        }
        return $this->state(fn (array $attributes) => [
            'history_able_id' => $history_able,
            'history_able_type' => get_class($history_able),
            'serie_id' => $serie_id
        ]);
    }
    /**
     * Create comment with specific author.
     * @param User $user
     * @return HistoryFactory
     */
    public function withUser(User $user): HistoryFactory
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user,
        ]);
    }
}
