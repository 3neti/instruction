# 3neti/instruction

Instruction Pricing Engine for Laravel

------------------------------------------------------------------------

## Overview

`3neti/instruction` is a standalone Laravel package that evaluates the
cost of instruction-based workflows.

It extracts the pricing logic from the x-change platform into a reusable
domain package.

### What it does

-   Calculates charges for instruction payloads
-   Supports dynamic instruction structures
-   Applies tariff-based pricing per component
-   Handles validation-based pricing rules
-   Computes slice-based fees for divisible vouchers

------------------------------------------------------------------------

## Installation

``` bash
composer require 3neti/instruction
```

Publish config and migrations:

``` bash
php artisan vendor:publish --tag=instruction-config
php artisan vendor:publish --tag=instruction-migrations
```

Run migrations:

``` bash
php artisan migrate
```

Seed canonical instruction items:

``` bash
php artisan db:seed --class="LBHurtado\Instruction\Database\Seeders\InstructionItemSeeder"
```

------------------------------------------------------------------------

## Usage

### Using the Action

``` php
use LBHurtado\Instruction\Actions\EvaluateInstructionCharges;
use LBHurtado\Instruction\Support\ArrayChargeableCustomer;
use LBHurtado\Instruction\Support\ArrayInstructionSource;

$customer = new ArrayChargeableCustomer([
    'email' => 'user@example.com',
]);

$instructions = new ArrayInstructionSource([
    'count' => 2,
    'inputs' => [
        'fields' => ['email'],
    ],
]);

$result = app(EvaluateInstructionCharges::class)
    ->handle($customer, $instructions);
```

------------------------------------------------------------------------

## API

### Endpoint

    POST /api/instruction/v1/estimate

### Request

``` json
{
  "customer": {
    "email": "user@example.com"
  },
  "instructions": {
    "count": 2,
    "inputs": {
      "fields": ["email"]
    }
  }
}
```

### Response

``` json
{
  "success": true,
  "data": {
    "charges": [],
    "total_amount": 0,
    "total_items_charged": 0,
    "currency": "PHP"
  },
  "meta": {}
}
```

------------------------------------------------------------------------

## Testing

``` bash
composer test
```

------------------------------------------------------------------------

## License

MIT
