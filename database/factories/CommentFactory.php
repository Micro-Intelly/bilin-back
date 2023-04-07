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
    #[ArrayShape(['title' => "string",'description' => "string",'body' => "string", 'author_id' => "\Database\Factories\UserFactory", 'type' => "mixed", 'commentable_id' => "mixed", 'commentable_type' => "mixed", 'root_comm_id' => "null", 'in_reply_to_id' => "null", 'serie_id' => "null"])]
    public function definition(): array    {
        $commentable = $this->commentable();
        return [
            'title' => fake()->words(rand(3, 10), true),
            'description' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'author_id' => User::factory(),
            'type' => fake()->randomElement(['comment', 'note']),
            'commentable_id' => $commentable::factory(),
            'commentable_type' => $commentable,
            'root_comm_id' => null,
            'in_reply_to_id' => null,
            'serie_id' => null
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
        $serie_id = null;
        if(str_ends_with(get_class($commentable), "Episode")){
            $serie_id = $commentable->serie_id;
        }
        return $this->state(fn (array $attributes) => [
            'commentable_id' => $commentable,
            'commentable_type' => get_class($commentable),
            'serie_id' => $serie_id
        ]);
    }
    /**
     * Create comment with specific root.
     * @param Comment $comment
     * @return CommentFactory
     */
    public function withRoot(Comment $comment): CommentFactory
    {
        return $this->state(fn (array $attributes) => [
            'root_comm_id' => $comment,
        ])->withCommentable($comment->commentable);
    }
    /**
     * Create comment with specific reply.
     * @param Comment $comment
     * @return CommentFactory
     */
    public function withInReplyTo(Comment $comment): CommentFactory
    {
        return $this->state(fn (array $attributes) => [
            'in_reply_to_id' => $comment,
        ])->withRoot($comment->root_comm);
    }
    /**
     * Create notes.
     * @return CommentFactory
     */
    public function isNote(): CommentFactory
    {
        return $this->state(fn (array $attributes) => [
            'body' => '<h1>Example Notes</h1><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8f/Example_image.svg/600px-Example_image.svg.png" alt="example" />',
            'type' => 'note'
        ]);
    }
    /**
     * Create comment.
     * @return CommentFactory
     */
    public function isComment(): CommentFactory
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'comment'
        ]);
    }
}
