<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Language>
 */
class LanguageFactory extends Factory
{
    /**
     * Define the model's default state.
     * @return array<string, mixed>
     */
    #[ArrayShape(['language' => "string", 'code' => "string", 'language_able_id' => "mixed", 'language_able_type' => "mixed"])]
    public function definition() : array
    {
        return [
            'language' => fake()->word(),
            'code' => fake()->word(),
        ];
    }
    /**
     * Method to create language with specific name and code
     * @param string $language
     * @param string $code
     * @return LanguageFactory
     */
    public function withLanCode(string $language, string $code): LanguageFactory
    {
        return $this->state(fn (array $attributes) => [
            'language' => $language,
            'code' => $code
        ]);
    }
}
