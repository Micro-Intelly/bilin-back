<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Episode;
use App\Models\Post;
use App\Models\Test;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Comment>
 */
class CommentFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    #[ArrayShape(['body' => "string", 'author_id' => "\Database\Factories\UserFactory", 'type' => "mixed", 'commentable_id' => "mixed", 'commentable_type' => "mixed"])]
    public function definition(): array
    {
        $commentable = $this->commentable();
        return [
            'body' => fake()->paragraph(),
            'author_id' => User::factory(),
            'type' => fake()->randomElement(['comment', 'note']),
            'commentable_id' => $commentable::factory(),
            'commentable_type' => $commentable,
        ];
    }
    /**
     * Return random commentable class.
     */
    public function commentable()
    {
        return $this->faker->randomElement([
            Post::class,
            Episode::class,
            Comment::class,
            Test::class
        ]);
    }
    /**
     * Create comment with specific author.
     * @param User $user
     * @return CommentFactory
     */
    public function withUser(User $user): CommentFactory
    {
        return $this->state(fn (array $attributes) => [
            'author_id' => $user,
        ]);
    }
    /**
     * Create comment with specific commentable.
     * @param mixed $commentable
     * @return CommentFactory
     */
    public function withCommentable(mixed $commentable): CommentFactory
    {
        return $this->state(fn (array $attributes) => [
            'commentable_id' => $commentable,
            'commentable_type' => class_basename($commentable),
        ]);
    }
}
