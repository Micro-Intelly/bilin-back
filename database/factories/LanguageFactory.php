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
    #[ArrayShape(['language' => "string", 'code' => "string", 'home_phrase' => "string"])]
    public function definition() : array
    {
        return [
            'language' => fake()->word(),
            'code' => fake()->unique()->word(),
            'home_phrase' => fake()->paragraph(1),
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
    /**
     * Method to create language with specific home phrase
     * @param string $phrase
     * @return LanguageFactory
     */
    public function withPhrase(string $phrase): LanguageFactory
    {
        return $this->state(fn (array $attributes) => [
            'home_phrase' => $phrase,
        ]);
    }
    /**
     * Method to create language with specific home image mini
     * @param string $path
     * @return LanguageFactory
     */
    public function withImageMini(string $path): LanguageFactory
    {
        return $this->state(fn (array $attributes) => [
            'home_img_mini' => $path,
        ]);
    }
    /**
     * Method to create language with specific home image
     * @param string $path
     * @return LanguageFactory
     */
    public function withImage(string $path): LanguageFactory
    {
        return $this->state(fn (array $attributes) => [
            'home_img' => $path,
        ]);
    }
}
