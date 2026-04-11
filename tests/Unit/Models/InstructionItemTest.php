<?php

use LBHurtado\Instruction\Models\InstructionItem;
use LBHurtado\Instruction\Tests\Fixtures\FakeChargeableCustomer;

it('can build default attributes from index', function () {
    $attributes = InstructionItem::attributesFromIndex('inputs.fields.email');

    expect($attributes)
        ->toHaveKey('index', 'inputs.fields.email')
        ->toHaveKey('name')
        ->toHaveKey('type')
        ->toHaveKey('price', 0)
        ->toHaveKey('currency', 'PHP');
});

it('returns category from meta', function () {
    $item = new InstructionItem([
        'meta' => ['category' => 'identity'],
    ]);

    expect($item->category)->toBe('identity');
});

it('defaults category to other', function () {
    $item = new InstructionItem([
        'meta' => [],
    ]);

    expect($item->category)->toBe('other');
});

it('returns meta product payload', function () {
    $item = new InstructionItem([
        'type' => 'inputs',
        'meta' => [
            'label' => 'Email',
            'description' => 'Charge for email input',
        ],
    ]);

    expect($item->getMetaProduct())
        ->toBe([
            'type' => 'inputs',
            'title' => 'Email',
            'description' => 'Charge for email input',
        ]);
});

it('returns zero for cash amount when customer is configured system user', function () {
    $item = new InstructionItem([
        'index' => 'cash.amount',
        'price' => 20,
    ]);

    $customer = new FakeChargeableCustomer(email: 'system@example.com');

    expect($item->getAmountProduct($customer))->toBe(0);
});

it('returns normal price for non system customer', function () {
    $item = new InstructionItem([
        'index' => 'cash.amount',
        'price' => 20,
    ]);

    $customer = new FakeChargeableCustomer(email: 'normal@example.com');

    expect($item->getAmountProduct($customer))->toBe(20.0);
});
