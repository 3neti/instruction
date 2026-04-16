<?php

use LBHurtado\Instruction\Models\InstructionItem;

it('can create an instruction item using the factory', function () {
    $item = InstructionItem::factory()->create();

    expect($item)->toBeInstanceOf(InstructionItem::class)
        ->and($item->index)->not->toBeEmpty()
        ->and($item->price)->toBeInstanceOf(\Brick\Money\Money::class);
});

it('can create a cash amount item using the factory state', function () {
    $item = InstructionItem::factory()->cashAmount()->create();

    expect($item->index)->toBe('cash.amount')
        ->and($item->price_minor)->toBe(2000)
        ->and($item->priceAsMoney()->getAmount()->__toString())->toBe('20.00');
});

it('can create an input field item using the factory state', function () {
    $item = InstructionItem::factory()->inputField('email', '7.50')->create();

    expect($item->index)->toBe('inputs.fields.email')
        ->and($item->price_minor)->toBe(750)
        ->and($item->priceAsMoney()->getAmount()->__toString())->toBe('7.50');
});