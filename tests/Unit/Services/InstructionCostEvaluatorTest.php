<?php

use LBHurtado\Instruction\Models\InstructionItem;
use LBHurtado\Instruction\Repositories\InstructionItemRepository;
use LBHurtado\Instruction\Services\InstructionCostEvaluator;
use LBHurtado\Instruction\Tests\Fixtures\ArrayInstructionSource;
use LBHurtado\Instruction\Tests\Fixtures\FakeChargeableCustomer;

beforeEach(function () {
    $this->repository = new InstructionItemRepository;
    $this->service = new InstructionCostEvaluator($this->repository);
    $this->customer = new FakeChargeableCustomer(email: 'user@example.com');
});

function makeItem(array $attributes): void
{
    InstructionItem::query()->create(array_merge([
        'name' => 'Item',
        'type' => 'general',
        'price' => 0,
        'currency' => 'PHP',
        'meta' => [],
    ], $attributes));
}

it('ignores excluded fields', function () {
    makeItem([
        'name' => 'Count',
        'index' => 'count',
        'price' => '10.00',
    ]);

    $result = $this->service->evaluate($this->customer, ['count' => 2]);

    expect($result)->toHaveCount(0);
});

it('charges truthy string values', function () {
    makeItem([
        'name' => 'Secret',
        'index' => 'secret',
        'price' => '10.00',
    ]);

    $result = $this->service->evaluate($this->customer, ['secret' => 'ABC123']);

    expect($result)->toHaveCount(1)
        ->and($result->first()->index)->toBe('secret')
        ->and($result->first()->price_minor)->toBe(1000)
        ->and($result->first()->toArray()['price'])->toBe(10.0);
});

it('does not charge empty string values', function () {
    makeItem([
        'name' => 'Secret',
        'index' => 'secret',
        'price' => '10.00',
    ]);

    $result = $this->service->evaluate($this->customer, ['secret' => '']);

    expect($result)->toHaveCount(0);
});

it('charges truthy boolean values', function () {
    makeItem([
        'name' => 'OTP',
        'index' => 'otp.required',
        'price' => '2.00',
    ]);

    $result = $this->service->evaluate($this->customer, ['otp' => ['required' => true]]);

    expect($result)->toHaveCount(1)
        ->and($result->first()->price_minor)->toBe(200)
        ->and($result->first()->toArray()['price'])->toBe(2.0);
});

it('charges positive integer values', function () {
    makeItem([
        'name' => 'Attempts',
        'index' => 'attempts',
        'price' => '4.00',
    ]);

    $result = $this->service->evaluate($this->customer, ['attempts' => 3]);

    expect($result)->toHaveCount(1)
        ->and($result->first()->price_minor)->toBe(400);
});

it('does not charge zero integer values', function () {
    makeItem([
        'name' => 'Attempts',
        'index' => 'attempts',
        'price' => '4.00',
    ]);

    $result = $this->service->evaluate($this->customer, ['attempts' => 0]);

    expect($result)->toHaveCount(0);
});

it('charges positive float values', function () {
    makeItem([
        'name' => 'Fee',
        'index' => 'fee',
        'price' => '7.00',
    ]);

    $result = $this->service->evaluate($this->customer, ['fee' => 1.5]);

    expect($result)->toHaveCount(1)
        ->and($result->first()->price_minor)->toBe(700);
});

it('charges non empty array values', function () {
    makeItem([
        'name' => 'Meta',
        'index' => 'meta',
        'price' => '9.00',
    ]);

    $result = $this->service->evaluate($this->customer, ['meta' => ['a' => 1]]);

    expect($result)->toHaveCount(1)
        ->and($result->first()->price_minor)->toBe(900);
});

it('does not charge zero priced items', function () {
    makeItem([
        'name' => 'Secret',
        'index' => 'secret',
        'price' => 0,
    ]);

    $result = $this->service->evaluate($this->customer, ['secret' => 'ABC123']);

    expect($result)->toHaveCount(0);
});

it('multiplies charge by count', function () {
    makeItem([
        'name' => 'Email',
        'index' => 'inputs.fields.email',
        'price' => '5.00',
    ]);

    $result = $this->service->evaluate($this->customer, [
        'count' => 3,
        'inputs' => [
            'fields' => ['email'],
        ],
    ]);

    expect($result)->toHaveCount(1)
        ->and($result->first()->quantity)->toBe(3)
        ->and($result->first()->unit_price_minor)->toBe(500)
        ->and($result->first()->price_minor)->toBe(1500)
        ->and($result->first()->toArray()['price'])->toBe(15.0);
});

it('uses meta label when present', function () {
    makeItem([
        'name' => 'Email',
        'index' => 'inputs.fields.email',
        'price' => 5,
        'meta' => ['label' => 'Email Address'],
    ]);

    $result = $this->service->evaluate($this->customer, [
        'inputs' => ['fields' => ['email']],
    ]);

    expect($result->first()->label)->toBe('Email Address');
});

it('charges selected input field from string values', function () {
    makeItem([
        'name' => 'Email',
        'index' => 'inputs.fields.email',
        'price' => 5,
    ]);

    $result = $this->service->evaluate($this->customer, [
        'inputs' => ['fields' => ['email']],
    ]);

    expect($result)->toHaveCount(1)
        ->and($result->first()->index)->toBe('inputs.fields.email');
});

it('charges selected input field case insensitively', function () {
    makeItem([
        'name' => 'Email',
        'index' => 'inputs.fields.email',
        'price' => 5,
    ]);

    $result = $this->service->evaluate($this->customer, [
        'inputs' => ['fields' => ['EMAIL']],
    ]);

    expect($result)->toHaveCount(1);
});

it('charges selected input field from enum like arrays', function () {
    makeItem([
        'name' => 'Email',
        'index' => 'inputs.fields.email',
        'price' => 5,
    ]);

    $result = $this->service->evaluate($this->customer, [
        'inputs' => [
            'fields' => [
                ['VoucherInputField' => 'email'],
            ],
        ],
    ]);

    expect($result)->toHaveCount(1);
});

it('reads nested cash validation values', function () {
    makeItem([
        'name' => 'OTP Validation',
        'index' => 'cash.validation.otp',
        'price' => '6.00',
    ]);

    $result = $this->service->evaluate($this->customer, [
        'cash' => [
            'validation' => [
                'otp' => true,
            ],
        ],
    ]);

    expect($result)->toHaveCount(1)
        ->and($result->first()->price_minor)->toBe(600);
});

it('charges validation item when required is true', function () {
    makeItem([
        'name' => 'Location Validation',
        'index' => 'validation.location',
        'price' => '2.00',
    ]);

    $result = $this->service->evaluate($this->customer, [
        'validation' => [
            'location' => ['required' => true],
        ],
    ]);

    expect($result)->toHaveCount(1)
        ->and($result->first()->price_minor)->toBe(200);
});

it('does not charge validation item when required is false', function () {
    makeItem([
        'name' => 'Location Validation',
        'index' => 'validation.location',
        'price' => 2,
    ]);

    $result = $this->service->evaluate($this->customer, [
        'validation' => [
            'location' => ['required' => false],
        ],
    ]);

    expect($result)->toHaveCount(0);
});

it('charges time validation item when window is configured', function () {
    makeItem([
        'name' => 'Time Validation',
        'index' => 'validation.time',
        'price' => '2.00',
    ]);

    $result = $this->service->evaluate($this->customer, [
        'validation' => [
            'time' => ['window' => ['08:00', '17:00']],
        ],
    ]);

    expect($result)->toHaveCount(1)
        ->and($result->first()->price_minor)->toBe(200);
});

it('charges time validation item when limit minutes is configured', function () {
    makeItem([
        'name' => 'Time Validation',
        'index' => 'validation.time',
        'price' => '2.00',
    ]);

    $result = $this->service->evaluate($this->customer, [
        'validation' => [
            'time' => ['limit_minutes' => 10],
        ],
    ]);

    expect($result)->toHaveCount(1)
        ->and($result->first()->price_minor)->toBe(200);
});

it('does not charge time validation item when empty', function () {
    makeItem([
        'name' => 'Time Validation',
        'index' => 'validation.time',
        'price' => 2,
    ]);

    $result = $this->service->evaluate($this->customer, [
        'validation' => [
            'time' => [],
        ],
    ]);

    expect($result)->toHaveCount(0);
});

it('adds fixed slice fee for additional slices only', function () {
    makeItem([
        'name' => 'Slice Fee',
        'index' => 'cash.slice_fee',
        'price' => '3.00',
    ]);

    $result = $this->service->evaluate($this->customer, [
        'count' => 2,
        'cash' => [
            'slice_mode' => 'fixed',
            'slices' => 4,
        ],
    ]);

    expect($result)->toHaveCount(1)
        ->and($result->first()->index)->toBe('cash.slice_fee')
        ->and($result->first()->slice_count)->toBe(3)
        ->and($result->first()->price_minor)->toBe(1800)
        ->and($result->first()->toArray()['price'])->toBe(18.0);
});

it('adds open slice fee for additional slices only', function () {
    makeItem([
        'name' => 'Slice Fee',
        'index' => 'cash.slice_fee',
        'price' => '3.00',
    ]);

    $result = $this->service->evaluate($this->customer, [
        'cash' => [
            'slice_mode' => 'open',
            'max_slices' => 5,
        ],
    ]);

    expect($result)->toHaveCount(1)
        ->and($result->first()->slice_count)->toBe(4)
        ->and($result->first()->price_minor)->toBe(1200)
        ->and($result->first()->toArray()['price'])->toBe(12.0);
});

it('does not add slice fee when there are no additional slices', function () {
    makeItem([
        'name' => 'Slice Fee',
        'index' => 'cash.slice_fee',
        'price' => 3,
    ]);

    $result = $this->service->evaluate($this->customer, [
        'cash' => [
            'slice_mode' => 'fixed',
            'slices' => 1,
        ],
    ]);

    expect($result)->toHaveCount(0);
});

it('returns charge estimate dto', function () {
    makeItem([
        'name' => 'Email',
        'index' => 'inputs.fields.email',
        'price' => '5.00',
    ]);

    $estimate = $this->service->estimate($this->customer, new ArrayInstructionSource([
        'inputs' => [
            'fields' => ['email'],
        ],
    ]));

    expect($estimate->total_items_charged)->toBe(1)
        ->and($estimate->total_amount_minor)->toBe(500)
        ->and($estimate->toArray()['total_amount'])->toBe(5.0)
        ->and($estimate->charges)->toHaveCount(1);
});