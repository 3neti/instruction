<?php

namespace LBHurtado\Instruction\Contracts;

interface ChargeableCustomer
{
    public function getChargeIdentifier(): string|int|null;

    public function getChargeEmail(): ?string;
}
