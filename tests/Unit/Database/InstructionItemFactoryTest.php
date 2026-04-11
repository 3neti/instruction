<?php

use LBHurtado\Instruction\Models\InstructionItem;

it('can create an instruction item using the factory', function () {
    $item = InstructionItem::factory()->create();

    expect($item)->toBeInstanceOf(InstructionItem::class)
        ->and($item->index)->not->toBeEmpty();
});

it('can create a cash amount item using the factory state', function () {
    $item = InstructionItem::factory()->cashAmount()->create();

    expect($item->index)->toBe('cash.amount')
        ->and($item->price)->toBe(20.0);
});

it('can create an input field item using the factory state', function () {
    $item = InstructionItem::factory()->inputField('email', 7.5)->create();

    expect($item->index)->toBe('inputs.fields.email')
        ->and($item->price)->toBe(7.5);
});
