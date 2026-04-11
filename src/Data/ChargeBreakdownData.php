<?php

namespace LBHurtado\Instruction\Data;

use Spatie\LaravelData\Data;

class ChargeBreakdownData extends Data
{
    public function __construct(
        public string $index,
        public mixed $value,
        public int|float|string $unit_price,
        public int $quantity,
        public int|float|string $price,
        public string $currency,
        public string $label,
        public int $pay_count = 1,
        public ?int $slice_count = null,
    ) {}
}
