<?php

namespace LBHurtado\Instruction\Tests\Fixtures;

use Bavix\Wallet\Interfaces\Customer;
use Bavix\Wallet\Traits\CanPay;
use Bavix\Wallet\Traits\HasWallet;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletCustomer extends Model implements Customer
{
    use HasFactory;
    use HasWallet;
    use CanPay;

    protected $table = 'wallet_customers';

    protected $guarded = [];

    public static function make(string $email): self
    {
        return static::query()->create([
            'email' => $email,
            'name' => 'Test Customer',
        ]);
    }
}