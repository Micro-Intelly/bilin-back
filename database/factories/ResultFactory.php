<?php

namespace Database\Factories;

use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Result>
 */
class ResultFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['result' => "float", 'user_id' => "\Database\Factories\UserFactory", 'test_id' => "\Database\Factories\TestFactory"])]
    public function definition(): array
    {
        return [
            'result' => fake()->randomFloat(2, 0, 10),
            'user_id' => User::factory(),
            'test_id' => Test::factory()
        ];
    }

    /**
     * Create comment with specific user.
     * @param User $user
     * @return ResultFactory
     */
    public function withUser(User $user): ResultFactory
    {
        return $this->state(fn (array $attributes) => [
            'author_id' => $user,
        ]);
    }
    /**
     * Create comment with specific test.
     * @param Test $test
     * @return ResultFactory
     */
    public function withTest(Test $test): ResultFactory
    {
        return $this->state(fn (array $attributes) => [
            'author_id' => $test,
        ]);
    }
}
