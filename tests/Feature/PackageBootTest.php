<?php

use LBHurtado\Instruction\Contracts\InstructionItemRepositoryContract;
use LBHurtado\Instruction\Repositories\InstructionItemRepository;

it('binds the repository contract', function () {
    expect(app(InstructionItemRepositoryContract::class))
        ->toBeInstanceOf(InstructionItemRepository::class);
});

it('has package config loaded', function () {
    expect(config('instruction'))->toBeArray()
        ->and(config('instruction.route.prefix'))->toBe('api/instruction/v1');
});
