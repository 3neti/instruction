<?php

use Illuminate\Support\Facades\Route;
use LBHurtado\Instruction\Http\Controllers\EstimateInstructionChargesController;

Route::prefix(config('instruction.route.prefix', 'api/instruction/v1'))
    ->middleware(config('instruction.route.middleware', ['api']))
    ->group(function () {
        Route::post('/estimate', EstimateInstructionChargesController::class)
            ->name('instruction.estimate');
    });
