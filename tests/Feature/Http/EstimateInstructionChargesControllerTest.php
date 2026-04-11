<?php

use LBHurtado\Instruction\Models\InstructionItem;

beforeEach(function () {
    InstructionItem::query()->create([
        'name' => 'Email',
        'index' => 'inputs.fields.email',
        'type' => 'fields',
        'price' => 5.00,
        'currency' => 'PHP',
        'meta' => [
            'label' => 'Email',
            'description' => 'Charge for email input field',
        ],
    ]);

    InstructionItem::query()->create([
        'name' => 'Slice Fee',
        'index' => 'cash.slice_fee',
        'type' => 'fee',
        'price' => 3.00,
        'currency' => 'PHP',
        'meta' => [
            'label' => 'Slice Fee',
            'description' => 'Additional fee for extra voucher slices',
        ],
    ]);
});

it('can estimate instruction charges via api', function () {
    $response = $this->postJson('/api/instruction/v1/estimate', [
        'customer' => [
            'id' => 1,
            'email' => 'user@example.com',
        ],
        'instructions' => [
            'count' => 2,
            'inputs' => [
                'fields' => ['email'],
            ],
        ],
    ]);

    $response
        ->assertOk()
        ->assertJson([
            'success' => true,
            'meta' => [],
        ])
        ->assertJsonPath('data.total_items_charged', 1)
        ->assertJsonPath('data.total_amount', 10)
        ->assertJsonPath('data.charges.0.index', 'inputs.fields.email')
        ->assertJsonPath('data.charges.0.quantity', 2)
        ->assertJsonPath('data.charges.0.price', 10);
});

it('can estimate slice fees via api', function () {
    $response = $this->postJson('/api/instruction/v1/estimate', [
        'customer' => [
            'email' => 'user@example.com',
        ],
        'instructions' => [
            'cash' => [
                'slice_mode' => 'fixed',
                'slices' => 4,
            ],
        ],
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('data.total_items_charged', 1)
        ->assertJsonPath('data.total_amount', 9)
        ->assertJsonPath('data.charges.0.index', 'cash.slice_fee')
        ->assertJsonPath('data.charges.0.slice_count', 3);
});

it('returns validation error when customer is missing', function () {
    $response = $this->postJson('/api/instruction/v1/estimate', [
        'instructions' => [
            'inputs' => [
                'fields' => ['email'],
            ],
        ],
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['customer']);
});

it('returns validation error when instructions are missing', function () {
    $response = $this->postJson('/api/instruction/v1/estimate', [
        'customer' => [
            'email' => 'user@example.com',
        ],
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['instructions']);
});

it('returns validation error when customer email is invalid', function () {
    $response = $this->postJson('/api/instruction/v1/estimate', [
        'customer' => [
            'email' => 'not-an-email',
        ],
        'instructions' => [
            'inputs' => [
                'fields' => ['email'],
            ],
        ],
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['customer.email']);
});

it('returns validation error when count is less than one', function () {
    $response = $this->postJson('/api/instruction/v1/estimate', [
        'customer' => [
            'email' => 'user@example.com',
        ],
        'instructions' => [
            'count' => 0,
        ],
    ]);

    $response
        ->assertStatus(422)
        ->assertJsonValidationErrors(['instructions.count']);
});
