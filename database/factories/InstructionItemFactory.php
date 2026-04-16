<?php

namespace LBHurtado\Instruction\Database\Factories;

use Brick\Money\Money;
use Illuminate\Database\Eloquent\Factories\Factory;
use LBHurtado\Instruction\Models\InstructionItem;

/**
 * @extends Factory<InstructionItem>
 */
class InstructionItemFactory extends Factory
{
    protected $model = InstructionItem::class;

    public function definition(): array
    {
        $index = 'inputs.fields.'.$this->faker->unique()->randomElement([
                'name',
                'email',
                'mobile',
                'address',
                'birth_date',
                'signature',
            ]);

        return array_merge(
            InstructionItem::attributesFromIndex($index),
            [
                'price' => Money::of(
                    amount: (string) $this->faker->randomFloat(2, 0, 50),
                    currency: 'PHP'
                ),
                'currency' => 'PHP',
                'meta' => [
                    'label' => str($index)->afterLast('.')->headline()->toString(),
                    'description' => 'Charge for '.$index,
                    'category' => 'general',
                ],
            ]
        );
    }

    public function withIndex(string $index, array $overrides = []): static
    {
        return $this->state(fn () => array_merge(
            InstructionItem::attributesFromIndex($index),
            [
                'meta' => [
                    'label' => str($index)->afterLast('.')->headline()->toString(),
                    'description' => 'Charge for '.$index,
                    'category' => 'general',
                ],
            ],
            $overrides
        ));
    }

    public function cashAmount(string|int|float $price = '20.00'): static
    {
        return $this->withIndex('cash.amount', [
            'name' => 'Cash Amount Fee',
            'type' => 'amount',
            'price' => Money::of((string) $price, 'PHP'),
            'meta' => [
                'label' => 'Cash Processing Fee',
                'description' => 'Processing fee for cash instruction',
                'category' => 'cash',
            ],
        ]);
    }

    public function sliceFee(string|int|float $price = '3.00'): static
    {
        return $this->withIndex('cash.slice_fee', [
            'name' => 'Slice Fee',
            'type' => 'fee',
            'price' => Money::of((string) $price, 'PHP'),
            'meta' => [
                'label' => 'Slice Fee',
                'description' => 'Additional fee for extra voucher slices',
                'category' => 'cash',
            ],
        ]);
    }

    public function validationLocation(string|int|float $price = '2.00'): static
    {
        return $this->withIndex('validation.location', [
            'name' => 'Location Validation',
            'type' => 'validation',
            'price' => Money::of((string) $price, 'PHP'),
            'meta' => [
                'label' => 'Location Validation',
                'description' => 'Charge for location validation',
                'category' => 'validation',
            ],
        ]);
    }

    public function validationTime(string|int|float $price = '2.00'): static
    {
        return $this->withIndex('validation.time', [
            'name' => 'Time Validation',
            'type' => 'validation',
            'price' => Money::of((string) $price, 'PHP'),
            'meta' => [
                'label' => 'Time Validation',
                'description' => 'Charge for time window or time limit validation',
                'category' => 'validation',
            ],
        ]);
    }

    public function inputField(string $field, string|int|float $price = '5.00'): static
    {
        return $this->withIndex("inputs.fields.{$field}", [
            'name' => str($field)->headline()->toString(),
            'type' => 'fields',
            'price' => Money::of((string) $price, 'PHP'),
            'meta' => [
                'label' => str($field)->headline()->toString(),
                'description' => "Charge for {$field} input field",
                'category' => 'inputs',
            ],
        ]);
    }
}