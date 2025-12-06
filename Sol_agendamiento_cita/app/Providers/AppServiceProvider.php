<?php

namespace App\Providers;

use App\Repository\Contract\CitaRepositoryInterface;
use App\Repository\Contract\TipoCitaRepositoryInterface;
use App\Repository\Contract\UsuarioRepositoryInterface;
use App\Repository\Impl\CitaRepository;
use App\Repository\Impl\TipoCitaRepository;
use App\Repository\Impl\UsuarioRepository;
use App\Services\Contract\AuthServiceInterface;
use App\Services\Contract\CitaServiceInterface;
use App\Services\Contract\TipoCitaServiceInterface;
use App\Services\Contract\UsuarioServiceInterface;
use App\Services\Impl\AuthService;
use App\Services\Impl\CitaService;
use App\Services\Impl\TipoCitaService;
use App\Services\Impl\UsuarioService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Repositories
        $this->app->bind(UsuarioRepositoryInterface::class, UsuarioRepository::class);
        $this->app->bind(CitaRepositoryInterface::class, CitaRepository::class);
        $this->app->bind(TipoCitaRepositoryInterface::class, TipoCitaRepository::class);

        // Services
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(CitaServiceInterface::class, CitaService::class);
        $this->app->bind(TipoCitaServiceInterface::class, TipoCitaService::class);
        $this->app->bind(UsuarioServiceInterface::class, UsuarioService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}

