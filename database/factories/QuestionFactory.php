<?php

namespace Database\Factories;

use App\Models\Test;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    #[ArrayShape(['question' => "string", 'answers' => "false|string", 'correct_answer' => "int", 'test_id' => "\Illuminate\Database\Eloquent\Factories\Factory"])]
    public function definition(): array
    {
        return [
            'question' => 'Test question',
            'answers' => json_encode([
                1 => 'question 1',
                2 => 'question 2',
                3 => 'question 3',
                4 => 'question 4'
            ]),
            'correct_answer' => rand(1, 4),
            'test_id' => Test::factory()
        ];
    }

    /**
     * Create comment with specific Test.
     * @param Test $test
     * @return QuestionFactory
     */
    public function withTest(Test $test): QuestionFactory
    {
        return $this->state(fn (array $attributes) => [
            'test_id' => $test,
        ]);
    }
}
