<?php

namespace LBHurtado\Instruction\Support;

use LBHurtado\Instruction\Contracts\ChargeableCustomer;

class ArrayChargeableCustomer implements ChargeableCustomer
{
    public function __construct(
        protected array $attributes = []
    ) {}

    public function getChargeIdentifier(): string|int|null
    {
        return $this->attributes['id'] ?? null;
    }

    public function getChargeEmail(): ?string
    {
        return $this->attributes['email'] ?? null;
    }

    public function toArray(): array
    {
        return $this->attributes;
    }
}
