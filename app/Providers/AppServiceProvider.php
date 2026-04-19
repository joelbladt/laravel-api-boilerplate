<?php

namespace App\Providers;

use App\Interfaces\BookRepositoryInterface;
use App\Interfaces\PublisherRepositoryInterface;
use App\Repositories\Auth\AuthTokenIssuerInterface;
use App\Repositories\BookRepository;
use App\Repositories\PublisherRepository;
use App\Services\Auth\AuthService;
use App\Services\Auth\SanctumTokenIssuer;
use App\Services\BookService;
use App\Services\PublisherService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthTokenIssuerInterface::class, SanctumTokenIssuer::class);
        $this->app->bind(AuthService::class, function (Application $app) {
            return new AuthService(
                $app->make(AuthTokenIssuerInterface::class),
            );
        });

        $this->app->bind(BookRepositoryInterface::class, BookRepository::class);
        $this->app->bind(BookService::class, function (Application $app) {
            return new BookService(
                $app->make(BookRepositoryInterface::class),
                $app->make(PublisherRepositoryInterface::class),
            );
        });

        $this->app->bind(PublisherRepositoryInterface::class, PublisherRepository::class);
        $this->app->bind(PublisherService::class, function (Application $app) {
            return new PublisherService($app->make(PublisherRepositoryInterface::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
