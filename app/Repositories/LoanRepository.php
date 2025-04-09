<?php

namespace App\Repositories;

use App\Enums\LoanStatusEnum;
use App\Interfaces\LoanRepositoryInterface;
use App\Models\Loan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class LoanRepository extends BaseRepository implements LoanRepositoryInterface
{
    /**
     * LoanRepository constructor.
     *
     * @param Loan $model
     */
    public function __construct(Loan $model)
    {
        parent::__construct($model);
    }

    /**
     * @inheritdoc
     */
    public function getActiveLoansForUser(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('status', LoanStatusEnum::ACTIVE)
            ->with('book')
            ->get();
    }

    /**
     * @inheritdoc
     */
    public function getDelayedLoans(): Collection
    {
        return $this->model
            ->where('status', LoanStatusEnum::ACTIVE)
            ->where('due_date', '<', Carbon::today())
            ->with(['user', 'book'])
            ->get();
    }

    /**
     * @inheritdoc
     */
    public function updateStatus(int $loanId, LoanStatusEnum $status): bool
    {
        $loan = $this->getById($loanId);
        return $loan->update(['status' => $status]);
    }

    /**
     * @inheritdoc
     */
    public function isBookBorrowed(int $bookId): bool
    {
        return $this->model
            ->where('book_id', $bookId)
            ->where('status', LoanStatusEnum::ACTIVE)
            ->exists();
    }

    /**
     * @inheritdoc
     */
    public function getDelayedLoansForUser(int $userId): Collection
    {
        return $this->model
            ->where('user_id', $userId)
            ->where('status', LoanStatusEnum::ACTIVE)
            ->where('due_date', '<', Carbon::today())
            ->with('book')
            ->get();
    }
}
