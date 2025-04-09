<?php

namespace Database\Factories;

use App\Enums\BookStatusEnum;
use App\Enums\LoanStatusEnum;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Garantir que temos um usuário e um livro disponível
        $user = User::factory()->create();
        $book = Book::factory()->available()->create();

        // Marcar o livro como emprestado
        $book->update(['status' => BookStatusEnum::BORROWED]);

        return [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'due_date' => Carbon::now()->addDays(14), // Padrão de 14 dias para devolução
            'status' => LoanStatusEnum::ACTIVE->value,
        ];
    }

    /**
     * Indicar que o empréstimo está atrasado.
     */
    public function delayed(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => Carbon::now()->subDays(5), // Data de devolução no passado
            'status' => LoanStatusEnum::DELAYED->value,
        ]);
    }

    /**
     * Indicar que o empréstimo foi devolvido.
     */
    public function returned(): static
    {
        return $this->state(function (array $attributes) {
            // Obter o livro associado e marcar como disponível
            $book = Book::find($attributes['book_id'] ?? null);
            if ($book) {
                $book->update(['status' => BookStatusEnum::AVAILABLE]);
            }

            return [
                'status' => LoanStatusEnum::RETURNED->value,
            ];
        });
    }

    /**
     * Criar um empréstimo para um usuário específico.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Criar um empréstimo para um livro específico.
     */
    public function forBook(Book $book): static
    {
        // Verificar se o livro está disponível
        if ($book->status !== BookStatusEnum::AVAILABLE) {
            throw new \Exception('O livro não está disponível para empréstimo');
        }

        // Marcar o livro como emprestado
        $book->update(['status' => BookStatusEnum::BORROWED]);

        return $this->state(fn (array $attributes) => [
            'book_id' => $book->id,
        ]);
    }

    /**
     * Define uma data de devolução personalizada.
     */
    public function dueDate(Carbon $date): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => $date,
        ]);
    }
}
