<?php

use LBHurtado\Instruction\Actions\EvaluateInstructionCharges;
use LBHurtado\Instruction\Models\InstructionItem;
use LBHurtado\Instruction\Repositories\InstructionItemRepository;
use LBHurtado\Instruction\Services\InstructionCostEvaluator;
use LBHurtado\Instruction\Tests\Fixtures\ArrayInstructionSource;
use LBHurtado\Instruction\Tests\Fixtures\FakeChargeableCustomer;

it('returns estimate data from the evaluator action', function () {
    InstructionItem::query()->create([
        'name' => 'Email',
        'index' => 'inputs.fields.email',
        'type' => 'fields',
        'price' => 5,
        'currency' => 'PHP',
        'meta' => [],
    ]);

    $action = new EvaluateInstructionCharges(
        new InstructionCostEvaluator(new InstructionItemRepository)
    );

    $result = $action->handle(
        new FakeChargeableCustomer,
        new ArrayInstructionSource([
            'inputs' => ['fields' => ['email']],
        ])
    );

    expect($result->total_items_charged)->toBe(1)
        ->and($result->total_amount)->toBe(5.0);
});
