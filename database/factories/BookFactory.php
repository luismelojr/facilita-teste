<?php

namespace Database\Factories;

use App\Enums\BookGenreEnum;
use App\Enums\BookStatusEnum;
use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Obter todos os valores do enum de gêneros
        $genres = array_column(BookGenreEnum::cases(), 'value');

        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'registration_number' => 'LIV' . $this->faker->unique()->numerify('#####'),
            'genre' => $this->faker->randomElement($genres),
            'status' => BookStatusEnum::AVAILABLE->value,
        ];
    }

    /**
     * Indicar que o livro está disponível.
     */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookStatusEnum::AVAILABLE->value,
        ]);
    }

    /**
     * Indicar que o livro está emprestado.
     */
    public function borrowed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BookStatusEnum::BORROWED->value,
        ]);
    }

    /**
     * Define o gênero do livro como ficção.
     */
    public function fiction(): static
    {
        return $this->state(fn (array $attributes) => [
            'genre' => BookGenreEnum::FICTION->value,
        ]);
    }

    /**
     * Define o gênero do livro como fantasia.
     */
    public function fantasy(): static
    {
        return $this->state(fn (array $attributes) => [
            'genre' => BookGenreEnum::FANTASY->value,
        ]);
    }

    /**
     * Define o gênero do livro como ficção científica.
     */
    public function scienceFiction(): static
    {
        return $this->state(fn (array $attributes) => [
            'genre' => BookGenreEnum::SCIENCE_FICTION->value,
        ]);
    }
}
