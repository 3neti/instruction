<?php

namespace LBHurtado\Instruction;

use Illuminate\Support\ServiceProvider;
use LBHurtado\Instruction\Contracts\InstructionItemRepositoryContract;
use LBHurtado\Instruction\Repositories\InstructionItemRepository;

class InstructionServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/instruction.php', 'instruction');

        $this->app->bind(
            InstructionItemRepositoryContract::class,
            InstructionItemRepository::class
        );
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/instruction.php' => config_path('instruction.php'),
        ], 'instruction-config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'instruction-migrations');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }
}
