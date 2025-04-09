<?php

namespace Tests\Unit\Services;

use App\Enums\LoanStatusEnum;
use App\Exceptions\BookNotAvailableException;
use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\LoanRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Services\BookService;
use App\Services\LoanService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class LoanServiceTest extends TestCase
{
    protected $loanRepository;
    protected $bookRepository;
    protected $userRepository;
    protected $bookService;
    protected $loanService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loanRepository = Mockery::mock(LoanRepositoryInterface::class);
        $this->bookRepository = Mockery::mock(BookRepositoryInterface::class);
        $this->userRepository = Mockery::mock(UserRepositoryInterface::class);
        $this->bookService = Mockery::mock(BookService::class);

        $this->loanService = new LoanService(
            $this->loanRepository,
            $this->bookRepository,
            $this->userRepository,
            $this->bookService
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_all_loans(): void
    {
        $loans = new Collection([
            (object)['id' => 1],
            (object)['id' => 2]
        ]);

        $this->loanRepository->shouldReceive('getAll')
            ->with(['*'], ['user', 'book'])
            ->once()
            ->andReturn($loans);

        $result = $this->loanService->getAllLoans();

        $this->assertCount(2, $result);
    }

    public function test_get_loan_by_id(): void
    {

        $loan = new Loan(['id' => 1]);
        $loanId = 1;

        $this->loanRepository->shouldReceive('getById')
            ->once()
            ->with(
                $loanId,
                ['*'],
                ['user', 'book']
            )
            ->andReturn($loan);


        $result = $this->loanService->getLoanById($loanId);


        $this->assertSame($loan, $result);
    }

    public function test_borrow_book_successfully(): void
    {

        $userId = 1;
        $bookId = 2;
        $dueDate = Carbon::now()->addDays(14);

        $book = Mockery::mock(Book::class);
        $book->shouldReceive('isAvailable')->once()->andReturn(true);

        $loan = new Loan(['id' => 1, 'user_id' => $userId, 'book_id' => $bookId]);

        $this->bookRepository->shouldReceive('getById')
            ->once()
            ->with($bookId)
            ->andReturn($book);

        $this->loanRepository->shouldReceive('create')
            ->once()
            ->with([
                'user_id' => $userId,
                'book_id' => $bookId,
                'due_date' => $dueDate,
                'status' => LoanStatusEnum::ACTIVE,
            ])
            ->andReturn($loan);

        $this->bookService->shouldReceive('markAsBorrowed')
            ->once()
            ->with($bookId)
            ->andReturn(true);


        $result = $this->loanService->borrowBook($userId, $bookId, $dueDate);


        $this->assertSame($loan, $result);
    }

    public function test_borrow_book_not_available(): void
    {

        $userId = 1;
        $bookId = 2;

        $book = Mockery::mock(Book::class);
        $book->shouldReceive('isAvailable')->once()->andReturn(false);

        $this->bookRepository->shouldReceive('getById')
            ->once()
            ->with($bookId)
            ->andReturn($book);


        $this->expectException(BookNotAvailableException::class);
        $this->loanService->borrowBook($userId, $bookId);
    }

    public function test_return_book(): void
    {

        $loanId = 1;
        $bookId = 2;

        $loan = new Loan(['id' => $loanId, 'book_id' => $bookId]);

        $this->loanRepository->shouldReceive('getById')
            ->once()
            ->with($loanId)
            ->andReturn($loan);

        $this->loanRepository->shouldReceive('updateStatus')
            ->once()
            ->with($loanId, LoanStatusEnum::RETURNED)
            ->andReturn(true);

        $this->bookService->shouldReceive('markAsAvailable')
            ->once()
            ->with($bookId)
            ->andReturn(true);


        $result = $this->loanService->returnBook($loanId);


        $this->assertTrue($result);
    }

    public function test_mark_as_delayed(): void
    {

        $loanId = 1;

        $this->loanRepository->shouldReceive('updateStatus')
            ->once()
            ->with($loanId, LoanStatusEnum::DELAYED)
            ->andReturn(true);


        $result = $this->loanService->markAsDelayed($loanId);


        $this->assertTrue($result);
    }

    public function test_get_active_loans_for_user(): void
    {

        $userId = 1;

        $loans = new Collection([
            new Loan(['id' => 1, 'user_id' => $userId]),
            new Loan(['id' => 2, 'user_id' => $userId])
        ]);

        $this->loanRepository->shouldReceive('getActiveLoansForUser')
            ->once()
            ->with($userId)
            ->andReturn($loans);


        $result = $this->loanService->getActiveLoansForUser($userId);


        $this->assertCount(2, $result);
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_get_delayed_loans(): void
    {

        $loans = new Collection([
            new Loan(['id' => 1, 'status' => LoanStatusEnum::DELAYED]),
            new Loan(['id' => 2, 'status' => LoanStatusEnum::DELAYED])
        ]);

        $this->loanRepository->shouldReceive('getDelayedLoans')
            ->once()
            ->andReturn($loans);


        $result = $this->loanService->getDelayedLoans();


        $this->assertCount(2, $result);
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function test_update_delayed_loans(): void
    {

        $fixedToday = Carbon::parse('2025-04-09');
        Carbon::setTestNow($fixedToday);

        $loan1 = Mockery::mock(Loan::class)->makePartial();
        $loan1->id = 1;
        $loan1->due_date = $fixedToday->copy()->subDays(1);

        $loan2 = Mockery::mock(Loan::class)->makePartial();
        $loan2->id = 2;
        $loan2->due_date = $fixedToday->copy()->subDays(2);

        $loans = new \Illuminate\Database\Eloquent\Collection([$loan1, $loan2]);

        $this->loanRepository->shouldReceive('findByCriteria')
            ->once()
            ->withArgs(function ($criteria) use ($fixedToday) {
                return
                    isset($criteria['status']) &&
                    $criteria['status'] === LoanStatusEnum::ACTIVE &&
                    isset($criteria[0]) &&
                    $criteria[0][0] === 'due_date' &&
                    $criteria[0][1] === '<' &&
                    $criteria[0][2] instanceof Carbon &&
                    $criteria[0][2]->equalTo($fixedToday);
            })
            ->andReturn($loans);

        $this->loanRepository->shouldReceive('updateStatus')
            ->times(2)
            ->with(
                Mockery::anyOf(1, 2),
                LoanStatusEnum::DELAYED
            )
            ->andReturn(true);


        $result = $this->loanService->updateDelayedLoans();


        $this->assertEquals(2, $result);


        Carbon::setTestNow();
    }

    public function test_has_delayed_loans(): void
    {

        $userId = 1;
        $delayedLoans = new Collection([
            new Loan(['id' => 1, 'user_id' => $userId, 'status' => LoanStatusEnum::DELAYED])
        ]);

        $this->loanRepository->shouldReceive('getDelayedLoansForUser')
            ->once()
            ->with($userId)
            ->andReturn($delayedLoans);


        $result = $this->loanService->hasDelayedLoans($userId);


        $this->assertTrue($result);
    }

    public function test_has_no_delayed_loans(): void
    {

        $userId = 1;
        $delayedLoans = new Collection([]);

        $this->loanRepository->shouldReceive('getDelayedLoansForUser')
            ->once()
            ->with($userId)
            ->andReturn($delayedLoans);


        $result = $this->loanService->hasDelayedLoans($userId);


        $this->assertFalse($result);
    }

    public function test_extend_loan(): void
    {

        $loanId = 1;
        $daysToExtend = 7;
        $oldDueDate = Carbon::now()->addDays(3);
        $newDueDate = (clone $oldDueDate)->addDays($daysToExtend);


        $loan = Mockery::mock(Loan::class)
            ->makePartial()
            ->expects()
            ->getAttribute('due_date')
            ->andReturn($oldDueDate)
            ->getMock();

        $this->loanRepository->shouldReceive('getById')
            ->once()
            ->with($loanId)
            ->andReturn($loan);

        $this->loanRepository->shouldReceive('update')
            ->once()
            ->with($loanId, Mockery::on(function ($arg) use ($newDueDate) {
                return isset($arg['due_date']) &&
                    $arg['due_date'] instanceof Carbon &&
                    $arg['due_date']->format('Y-m-d') === $newDueDate->format('Y-m-d');
            }))
            ->andReturn(true);


        $result = $this->loanService->extendLoan($loanId, $daysToExtend);


        $this->assertTrue($result);
    }
}
