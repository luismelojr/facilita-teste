<?php

namespace App\Interfaces;

use App\Enums\LoanStatusEnum;
use Illuminate\Database\Eloquent\Collection;

interface LoanRepositoryInterface extends RepositoryInterface
{
    /**
     * Obter empréstimos ativos de um usuário
     *
     * @param int $userId ID do usuário
     * @return Collection
     */
    public function getActiveLoansForUser(int $userId): Collection;

    /**
     * Obter empréstimos atrasados
     *
     * @return Collection
     */
    public function getDelayedLoans(): Collection;

    /**
     * Atualizar status do empréstimo
     *
     * @param int $loanId ID do empréstimo
     * @param LoanStatusEnum $status Novo status
     * @return bool
     */
    public function updateStatus(int $loanId, LoanStatusEnum $status): bool;

    /**
     * Verificar se livro está emprestado
     *
     * @param int $bookId ID do livro
     * @return bool
     */
    public function isBookBorrowed(int $bookId): bool;

    /**
     * Verificar empréstimos atrasados para um usuário
     *
     * @param int $userId ID do usuário
     * @return Collection
     */
    public function getDelayedLoansForUser(int $userId): Collection;
}
