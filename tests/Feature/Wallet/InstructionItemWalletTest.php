<?php

use LBHurtado\Instruction\Models\InstructionItem;
use LBHurtado\Instruction\Tests\Fixtures\WalletCustomer;

it('starts with zero wallet balance', function () {
    $item = InstructionItem::factory()->cashAmount()->create();

    expect($item->balanceInt)->toBe(0);
});

it('can receive funds into the instruction item wallet', function () {
    $item = InstructionItem::factory()->cashAmount()->create();

    $item->deposit(1500, ['reason' => 'pending revenue']);

    expect($item->balanceInt)->toBe(1500);
});

it('can be purchased by a wallet customer and receive funds', function () {
    $item = InstructionItem::factory()->inputField('email', '5.00')->create();
    $customer = WalletCustomer::make('buyer@example.com');

    $customer->deposit(10000);
    $customer->pay($item);

    expect($customer->balanceInt)->toBe(9500)
        ->and($item->balanceInt)->toBe(500);
});