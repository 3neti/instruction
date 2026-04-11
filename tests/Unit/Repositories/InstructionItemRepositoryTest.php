<?php

use LBHurtado\Instruction\Models\InstructionItem;
use LBHurtado\Instruction\Repositories\InstructionItemRepository;

beforeEach(function () {
    $this->repository = new InstructionItemRepository();

    InstructionItem::query()->create([
        'name' => 'Email',
        'index' => 'inputs.fields.email',
        'type' => 'fields',
        'price' => 5,
        'currency' => 'PHP',
        'meta' => ['description' => 'Email field'],
    ]);

    InstructionItem::query()->create([
        'name' => 'Signature',
        'index' => 'inputs.fields.signature',
        'type' => 'fields',
        'price' => 3,
        'currency' => 'PHP',
        'meta' => ['description' => 'Signature field'],
    ]);

    InstructionItem::query()->create([
        'name' => 'Location Validation',
        'index' => 'validation.location',
        'type' => 'validation',
        'price' => 2,
        'currency' => 'PHP',
        'meta' => ['description' => 'Location validation'],
    ]);
});

it('can return all items', function () {
    expect($this->repository->all())->toHaveCount(3);
});

it('can find item by index', function () {
    $item = $this->repository->findByIndex('inputs.fields.email');

    expect($item)->not->toBeNull()
        ->and($item->name)->toBe('Email');
});

it('can find items by indices', function () {
    $items = $this->repository->findByIndices([
        'inputs.fields.email',
        'validation.location',
    ]);

    expect($items)->toHaveCount(2);
});

it('can return items by type', function () {
    $items = $this->repository->allByType('fields');

    expect($items)->toHaveCount(2);
});

it('can total charges by indices', function () {
    $total = $this->repository->totalCharge([
        'inputs.fields.email',
        'inputs.fields.signature',
    ]);

    expect($total)->toBe(8.0);
});

it('can return descriptions for indices', function () {
    $descriptions = $this->repository->descriptionsFor([
        'inputs.fields.email',
        'validation.location',
    ]);

    expect($descriptions)->toBe([
        'inputs.fields.email' => 'Email field',
        'validation.location' => 'Location validation',
    ]);
});
