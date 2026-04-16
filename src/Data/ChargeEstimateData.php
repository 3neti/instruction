<?php

namespace LBHurtado\Instruction\Data;

use Brick\Money\Money;
use Spatie\LaravelData\Data;

class ChargeEstimateData extends Data
{
    /**
     * @param  array<ChargeBreakdownData>  $charges
     */
    public function __construct(
        public array $charges,
        public int $total_amount_minor,
        public int $total_items_charged,
        public string $currency = 'PHP',
    ) {}

    public function getTotalAmount(): float
    {
        return Money::ofMinor($this->total_amount_minor, $this->currency)
            ->getAmount()
            ->toFloat();
    }

    public function toArray(): array
    {
        return [
            'charges' => array_map(
                fn ($charge) => $charge instanceof ChargeBreakdownData ? $charge->toArray() : $charge,
                $this->charges
            ),
            'total_amount' => $this->getTotalAmount(),
            'total_amount_minor' => $this->total_amount_minor,
            'total_items_charged' => $this->total_items_charged,
            'currency' => $this->currency,
        ];
    }
}