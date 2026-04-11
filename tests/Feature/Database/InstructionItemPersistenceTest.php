<?php

use LBHurtado\Instruction\Models\InstructionItem;

it('can persist an instruction item', function () {
    $item = InstructionItem::query()->create([
        'name' => 'Email',
        'index' => 'inputs.fields.email',
        'type' => 'fields',
        'price' => 5,
        'currency' => 'PHP',
        'meta' => ['label' => 'Email Address'],
    ]);

    expect($item->exists)->toBeTrue();

    $this->assertDatabaseHas('instruction_items', [
        'id' => $item->id,
        'index' => 'inputs.fields.email',
        'type' => 'fields',
    ]);
});
