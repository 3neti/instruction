<?php

namespace LBHurtado\Instruction\Data;

use Spatie\LaravelData\Data;

class ChargeEstimateData extends Data
{
    /**
     * @param  array<ChargeBreakdownData>  $charges
     */
    public function __construct(
        public array $charges,
        public int|float|string $total_amount,
        public int $total_items_charged,
        public string $currency = 'PHP',
    ) {}
}
