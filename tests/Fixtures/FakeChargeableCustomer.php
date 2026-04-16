<?php

namespace LBHurtado\Instruction\Tests\Fixtures;

use Bavix\Wallet\Interfaces\Customer;
use Bavix\Wallet\Traits\CanPay;
use LBHurtado\Instruction\Contracts\ChargeableCustomer;

class FakeChargeableCustomer implements ChargeableCustomer, Customer
{
    use CanPay;

    public function __construct(
        protected string|int|null $id = 1,
        protected ?string $email = 'user@example.com',
    ) {}

    public function getChargeIdentifier(): string|int|null
    {
        return $this->id;
    }

    public function getChargeEmail(): ?string
    {
        return $this->email;
    }
}
