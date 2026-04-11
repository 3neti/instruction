<?php

use LBHurtado\Instruction\Database\Seeders\InstructionItemSeeder;
use LBHurtado\Instruction\Models\InstructionItem;

it('seeds canonical instruction items', function () {
    $this->seed(InstructionItemSeeder::class);

    expect(InstructionItem::query()->where('index', 'cash.amount')->exists())->toBeTrue()
        ->and(InstructionItem::query()->where('index', 'cash.slice_fee')->exists())->toBeTrue()
        ->and(InstructionItem::query()->where('index', 'inputs.fields.email')->exists())->toBeTrue()
        ->and(InstructionItem::query()->where('index', 'validation.location')->exists())->toBeTrue();
});

it('can seed idempotently', function () {
    $this->seed(InstructionItemSeeder::class);
    $this->seed(InstructionItemSeeder::class);

    expect(
        InstructionItem::query()->where('index', 'cash.amount')->count()
    )->toBe(1);
});
