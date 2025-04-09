<?php

namespace Tests\Unit\Repositories;

use App\Enums\LoanStatusEnum;
use App\Models\Book;
use App\Models\Loan;
use App\Models\User;
use App\Repositories\LoanRepository;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private LoanRepository $loanRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loanRepository = new LoanRepository(new Loan());
    }


    public function test_it_can_get_all_loans()
    {

        Loan::factory()->count(3)->create();


        $result = $this->loanRepository->getAll();


        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }


    public function test_it_can_get_loan_by_id()
    {

        $user = User::factory()->create();
        $book = Book::factory()->create();

        $loan = Loan::factory()->create([
            'user_id' => $user->id,
            'book_id' => $book->id,
            'due_date' => Carbon::now()->addDays(7),
            'status' => LoanStatusEnum::ACTIVE->value
        ]);


        $foundLoan = $this->loanRepository->getById($loan->id);


        $this->assertInstanceOf(Loan::class, $foundLoan);
        $this->assertEquals($loan->id, $foundLoan->id);
        $this->assertEquals($user->id, $foundLoan->user_id);
        $this->assertEquals($book->id, $foundLoan->book_id);
        $this->assertEquals(LoanStatusEnum::ACTIVE, $foundLoan->status);
    }


    public function test_it_can_create_a_loan()
    {

        $user = User::factory()->create();
        $book = Book::factory()->available()->create();
        $dueDate = Carbon::now()->addDays(14);

        $loanData = [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'due_date' => $dueDate,
            'status' => LoanStatusEnum::ACTIVE->value
        ];


        $loan = $this->loanRepository->create($loanData);


        $this->assertInstanceOf(Loan::class, $loan);
        $this->assertDatabaseHas('loans', [
            'user_id' => $user->id,
            'book_id' => $book->id,
            'status' => LoanStatusEnum::ACTIVE->value
        ]);
    }


    public function test_it_can_update_a_loan()
    {

        $loan = Loan::factory()->create([
            'status' => LoanStatusEnum::ACTIVE->value,
            'due_date' => Carbon::now()->addDays(7)
        ]);

        $newDueDate = Carbon::now()->addDays(14);
        $updateData = [
            'due_date' => $newDueDate,
            'status' => LoanStatusEnum::ACTIVE->value
        ];


        $result = $this->loanRepository->update($loan->id, $updateData);


        $this->assertTrue($result);
        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'due_date' => $newDueDate->format('Y-m-d H:i:s'),
            'status' => LoanStatusEnum::ACTIVE->value
        ]);
    }


    public function test_it_can_delete_a_loan()
    {

        $loan = Loan::factory()->create();


        $result = $this->loanRepository->delete($loan->id);


        $this->assertTrue($result);
        $this->assertDatabaseMissing('loans', ['id' => $loan->id]);
    }


    public function test_it_can_get_active_loans_for_user()
    {

        $user = User::factory()->create();


        Loan::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => LoanStatusEnum::ACTIVE->value
        ]);


        Loan::factory()->create([
            'user_id' => $user->id,
            'status' => LoanStatusEnum::RETURNED->value
        ]);


        Loan::factory()->create([
            'status' => LoanStatusEnum::ACTIVE->value
        ]);


        $activeLoans = $this->loanRepository->getActiveLoansForUser($user->id);


        $this->assertInstanceOf(Collection::class, $activeLoans);
        $this->assertCount(2, $activeLoans);

        foreach ($activeLoans as $loan) {
            $this->assertEquals($user->id, $loan->user_id);
            $this->assertEquals(LoanStatusEnum::ACTIVE, $loan->status);
        }
    }


    public function test_it_can_get_delayed_loans()
    {

        Carbon::setTestNow('2025-04-09');


        Loan::factory()->create([
            'due_date' => Carbon::now()->subDays(5),
            'status' => LoanStatusEnum::ACTIVE->value
        ]);

        Loan::factory()->create([
            'due_date' => Carbon::now()->subDays(2),
            'status' => LoanStatusEnum::ACTIVE->value
        ]);


        Loan::factory()->create([
            'due_date' => Carbon::now()->addDays(3),
            'status' => LoanStatusEnum::ACTIVE->value
        ]);

        Loan::factory()->create([
            'due_date' => Carbon::now()->subDays(10),
            'status' => LoanStatusEnum::ACTIVE->value
        ]);


        $delayedLoans = $this->loanRepository->getDelayedLoans();


        $this->assertInstanceOf(Collection::class, $delayedLoans);
        $this->assertCount(3, $delayedLoans);

        foreach ($delayedLoans as $loan) {
            $this->assertTrue(
                $loan->status === LoanStatusEnum::DELAYED ||
                ($loan->status === LoanStatusEnum::ACTIVE && $loan->due_date->lessThan(Carbon::now()))
            );
        }


        Carbon::setTestNow();
    }


    public function test_it_can_update_loan_status()
    {

        $loan = Loan::factory()->create([
            'status' => LoanStatusEnum::ACTIVE->value
        ]);


        $result = $this->loanRepository->updateStatus($loan->id, LoanStatusEnum::DELAYED);


        $this->assertTrue($result);
        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'status' => LoanStatusEnum::DELAYED->value
        ]);
    }


    public function test_it_can_check_if_book_is_borrowed()
    {

        $book = Book::factory()->create();


        Loan::factory()->create([
            'book_id' => $book->id,
            'status' => LoanStatusEnum::ACTIVE->value
        ]);


        $isBorrowed = $this->loanRepository->isBookBorrowed($book->id);


        $this->assertTrue($isBorrowed);
    }


    public function test_it_can_verify_book_is_not_borrowed()
    {

        $book = Book::factory()->create();


        Loan::factory()->create([
            'book_id' => $book->id,
            'status' => LoanStatusEnum::RETURNED->value
        ]);


        $isBorrowed = $this->loanRepository->isBookBorrowed($book->id);


        $this->assertFalse($isBorrowed);
    }


    public function test_it_can_get_delayed_loans_for_user()
    {

        Carbon::setTestNow('2025-04-09');

        $user = User::factory()->create();


        Loan::factory()->create([
            'user_id' => $user->id,
            'due_date' => Carbon::now()->subDays(5),
            'status' => LoanStatusEnum::ACTIVE->value
        ]);


        Loan::factory()->create([
            'user_id' => $user->id,
            'due_date' => Carbon::now()->addDays(5),
            'status' => LoanStatusEnum::ACTIVE->value
        ]);


        Loan::factory()->create([
            'due_date' => Carbon::now()->subDays(3),
            'status' => LoanStatusEnum::ACTIVE->value
        ]);


        Loan::factory()->create([
            'user_id' => $user->id,
            'due_date' => Carbon::now()->subDays(10),
            'status' => LoanStatusEnum::DELAYED->value
        ]);


        $delayedLoans = $this->loanRepository->getDelayedLoansForUser($user->id);


        $this->assertInstanceOf(Collection::class, $delayedLoans);
        $this->assertCount(1, $delayedLoans);
        $this->assertEquals($user->id, $delayedLoans->first()->user_id);
        $this->assertEquals(LoanStatusEnum::ACTIVE, $delayedLoans->first()->status);
        $this->assertTrue($delayedLoans->first()->due_date->lessThan(Carbon::today()));


        Carbon::setTestNow();
    }


    public function test_it_can_find_by_criteria()
    {

        $user = User::factory()->create();


        Loan::factory()->create([
            'user_id' => $user->id,
            'status' => LoanStatusEnum::ACTIVE->value,
            'due_date' => Carbon::now()->addDays(5)
        ]);

        Loan::factory()->create([
            'user_id' => $user->id,
            'status' => LoanStatusEnum::DELAYED->value,
            'due_date' => Carbon::now()->subDays(3)
        ]);


        $criteria = [
            'user_id' => $user->id,
            'status' => LoanStatusEnum::ACTIVE->value
        ];

        $loans = $this->loanRepository->findByCriteria($criteria);


        $this->assertInstanceOf(Collection::class, $loans);
        $this->assertCount(1, $loans);
        $this->assertEquals($user->id, $loans->first()->user_id);
        $this->assertEquals(LoanStatusEnum::ACTIVE, $loans->first()->status);
    }
}
