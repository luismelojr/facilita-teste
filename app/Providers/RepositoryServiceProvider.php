<?php

namespace App\Providers;

use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\LoanRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\BookRepository;
use App\Repositories\LoanRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(BookRepositoryInterface::class, BookRepository::class);
        $this->app->bind(LoanRepositoryInterface::class, LoanRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
