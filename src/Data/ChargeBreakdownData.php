<?php

namespace LBHurtado\Instruction\Data;

use Brick\Money\Money;
use Spatie\LaravelData\Data;

class ChargeBreakdownData extends Data
{
    public function __construct(
        public string $index,
        public mixed $value,
        public int $unit_price_minor,
        public int $quantity,
        public int $price_minor,
        public string $currency,
        public string $label,
        public int $pay_count = 1,
        public ?int $slice_count = null,
    ) {}

    public function getUnitPrice(): float
    {
        return Money::ofMinor($this->unit_price_minor, $this->currency)
            ->getAmount()
            ->toFloat();
    }

    public function getPrice(): float
    {
        return Money::ofMinor($this->price_minor, $this->currency)
            ->getAmount()
            ->toFloat();
    }

    public function toArray(): array
    {
        return [
            'index' => $this->index,
            'value' => $this->value,
            'unit_price' => $this->getUnitPrice(),
            'unit_price_minor' => $this->unit_price_minor,
            'quantity' => $this->quantity,
            'price' => $this->getPrice(),
            'price_minor' => $this->price_minor,
            'currency' => $this->currency,
            'label' => $this->label,
            'pay_count' => $this->pay_count,
            'slice_count' => $this->slice_count,
        ];
    }
}