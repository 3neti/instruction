<?php

use Brick\Money\Money;
use LBHurtado\Instruction\Models\InstructionItem;

it('can persist an instruction item in minor units', function () {
    $item = InstructionItem::query()->create([
        'name' => 'Email',
        'index' => 'inputs.fields.email',
        'type' => 'fields',
        'price' => Money::of('5.00', 'PHP'),
        'currency' => 'PHP',
        'meta' => ['label' => 'Email Address'],
    ]);

    expect($item->exists)->toBeTrue()
        ->and($item->price_minor)->toBe(500)
        ->and($item->priceAsMoney()->getAmount()->__toString())->toBe('5.00');

    $this->assertDatabaseHas('instruction_items', [
        'id' => $item->id,
        'index' => 'inputs.fields.email',
        'type' => 'fields',
        'price' => 500,
    ]);
});