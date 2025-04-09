<?php

namespace App\Services;

use App\Enums\LoanStatusEnum;
use App\Exceptions\BookNotAvailableException;
use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\LoanRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class LoanService
{
    /**
     * LoanService constructor.
     *
     * @param LoanRepositoryInterface $loanRepository
     * @param BookRepositoryInterface $bookRepository
     * @param UserRepositoryInterface $userRepository
     * @param BookService $bookService
     */
    public function __construct(
        protected LoanRepositoryInterface $loanRepository,
        protected BookRepositoryInterface $bookRepository,
        protected UserRepositoryInterface $userRepository,
        protected BookService $bookService
    ){}

    /**
     * Obter todos os empréstimos
     *
     * @return Collection
     */
    public function getAllLoans(): Collection
    {
        return $this->loanRepository->getAll(
            relations: ['user', 'book']
        );
    }

    /**
     * Obter empréstimo por ID
     *
     * @param int $loanId
     * @return Model
     */
    public function getLoanById(int $loanId): Model
    {
        return $this->loanRepository->getById(
            id: $loanId,
            relations: ['user', 'book']
        );
    }

    /**
     * Emprestar livro para usuário
     *
     * @param int $userId
     * @param int $bookId
     * @param Carbon|null $dueDate
     * @return Model
     * @throws BookNotAvailableException
     */
    public function borrowBook(int $userId, int $bookId, ?Carbon $dueDate = null): Model
    {
        // Verificar se o livro está disponível
        $book = $this->bookRepository->getById($bookId);

        if (!$book->isAvailable()) {
            throw new BookNotAvailableException("O livro não está disponível para empréstimo.");
        }

        // Definir data de devolução padrão (14 dias a partir de hoje)
        if (!$dueDate) {
            $dueDate = Carbon::now()->addDays(14);
        }

        // Criar o empréstimo
        $loan = $this->loanRepository->create([
            'user_id' => $userId,
            'book_id' => $bookId,
            'due_date' => $dueDate,
            'status' => LoanStatusEnum::ACTIVE,
        ]);

        // Atualizar status do livro para emprestado
        $this->bookService->markAsBorrowed($bookId);

        return $loan;
    }

    /**
     * Devolver livro
     *
     * @param int $loanId
     * @return bool
     */
    public function returnBook(int $loanId): bool
    {
        $loan = $this->loanRepository->getById($loanId);

        // Atualizar status do empréstimo para devolvido
        $this->loanRepository->updateStatus($loanId, LoanStatusEnum::RETURNED);

        // Atualizar status do livro para disponível
        $this->bookService->markAsAvailable($loan->book_id);

        return true;
    }

    /**
     * Marcar empréstimo como atrasado
     *
     * @param int $loanId
     * @return bool
     */
    public function markAsDelayed(int $loanId): bool
    {
        return $this->loanRepository->updateStatus($loanId, LoanStatusEnum::DELAYED);
    }

    /**
     * Obter empréstimos ativos do usuário
     *
     * @param int $userId
     * @return Collection
     */
    public function getActiveLoansForUser(int $userId): Collection
    {
        return $this->loanRepository->getActiveLoansForUser($userId);
    }

    /**
     * Obter todos os empréstimos atrasados
     *
     * @return Collection
     */
    public function getDelayedLoans(): Collection
    {
        return $this->loanRepository->getDelayedLoans();
    }

    /**
     * Verificar e atualizar status de empréstimos atrasados
     *
     * @return int Número de empréstimos atualizados
     */
    public function updateDelayedLoans(): int
    {
        $today = Carbon::today();
        $delayedCount = 0;

        // Buscar empréstimos ativos com data de devolução no passado
        $loans = $this->loanRepository->findByCriteria([
            'status' => LoanStatusEnum::ACTIVE,
            ['due_date', '<', $today]
        ]);

        // Atualizar status para atrasado
        foreach ($loans as $loan) {
            $this->loanRepository->updateStatus($loan->id, LoanStatusEnum::DELAYED);
            $delayedCount++;
        }

        return $delayedCount;
    }

    /**
     * Verificar se um usuário possui empréstimos atrasados
     *
     * @param int $userId
     * @return bool
     */
    public function hasDelayedLoans(int $userId): bool
    {
        $delayedLoans = $this->loanRepository->getDelayedLoansForUser($userId);
        return $delayedLoans->isNotEmpty();
    }

    /**
     * Prorrogar data de devolução de um empréstimo
     *
     * @param int $loanId
     * @param int $daysToExtend
     * @return bool
     */
    public function extendLoan(int $loanId, int $daysToExtend = 7): bool
    {
        $loan = $this->loanRepository->getById($loanId);

        // Calcular nova data de devolução
        $newDueDate = Carbon::parse($loan->due_date)->addDays($daysToExtend);

        return $this->loanRepository->update($loanId, [
            'due_date' => $newDueDate
        ]);
    }
}
