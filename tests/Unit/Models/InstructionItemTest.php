<?php

use Bavix\Wallet\Interfaces\ProductInterface;
use Brick\Money\Money;
use LBHurtado\Instruction\Models\InstructionItem;
use LBHurtado\Instruction\Tests\Fixtures\WalletCustomer;

it('implements the bavix product interface', function () {
    expect(new InstructionItem())->toBeInstanceOf(ProductInterface::class);
});

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

it('returns meta product payload with money metadata', function () {
    $item = new InstructionItem([
        'type' => 'inputs',
        'currency' => 'PHP',
        'price' => Money::of('5.00', 'PHP'),
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
            'currency' => 'PHP',
            'price_minor' => 500,
            'price_decimal' => '5.00',
        ]);
});

it('returns zero for cash amount when customer is configured system user', function () {
    $item = new InstructionItem([
        'index' => 'cash.amount',
        'currency' => 'PHP',
        'price' => Money::of('20.00', 'PHP'),
    ]);

    $customer = WalletCustomer::make('system@example.com');

    expect($item->getAmountProduct($customer))->toBe(0);
});

it('returns normal minor-unit price for non system customer', function () {
    $item = new InstructionItem([
        'index' => 'cash.amount',
        'currency' => 'PHP',
        'price' => Money::of('20.00', 'PHP'),
    ]);

    $customer = WalletCustomer::make('normal@example.com');

    expect($item->getAmountProduct($customer))->toBe(2000);
});

it('exposes price as brick money', function () {
    $item = new InstructionItem([
        'currency' => 'PHP',
        'price' => 1234,
    ]);

    expect($item->priceAsMoney()->getAmount()->__toString())->toBe('12.34')
        ->and($item->priceAsMoney()->getMinorAmount()->toInt())->toBe(1234);
});

it('accepts decimal strings and stores price as minor units', function () {
    $item = new InstructionItem([
        'currency' => 'PHP',
        'price' => '7.50',
    ]);

    expect($item->price_minor)->toBe(750);
});