<?php

use Illuminate\Support\Facades\Route;

Route::prefix(config('instruction.route.prefix', 'api/instruction/v1'))
    ->middleware(config('instruction.route.middleware', ['api']))
    ->group(function () {
        // placeholder
    });
