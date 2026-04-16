<?php

use Brick\Money\Money;
use Illuminate\Support\Carbon;
use LBHurtado\Instruction\Models\InstructionItem;
use LBHurtado\Instruction\Models\InstructionItemPriceHistory;

it('can persist price history records in minor units', function () {
    $item = InstructionItem::factory()->cashAmount()->create();

    $history = InstructionItemPriceHistory::query()->create([
        'instruction_item_id' => $item->id,
        'old_price' => Money::of('20.00', 'PHP')->getMinorAmount()->toInt(),
        'new_price' => Money::of('25.00', 'PHP')->getMinorAmount()->toInt(),
        'currency' => 'PHP',
        'changed_by' => 'tester@example.com',
        'reason' => 'Tariff update',
        'effective_at' => Carbon::parse('2026-04-16 09:00:00'),
    ]);

    expect($history->instructionItem)->toBeInstanceOf(InstructionItem::class)
        ->and($history->oldPriceMoney()->getAmount()->__toString())->toBe('20.00')
        ->and($history->newPriceMoney()->getAmount()->__toString())->toBe('25.00');

    $this->assertDatabaseHas('instruction_item_price_histories', [
        'id' => $history->id,
        'instruction_item_id' => $item->id,
        'old_price' => 2000,
        'new_price' => 2500,
        'currency' => 'PHP',
    ]);
});