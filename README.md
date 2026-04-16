# 3neti/instruction

Instruction Pricing Engine for Laravel

------------------------------------------------------------------------

## Overview

`3neti/instruction` is a standalone Laravel package that evaluates the
cost of instruction-based workflows.

It extracts the pricing and charge computation logic from the x-change platform into a reusable domain package.

### What it does

- Calculates charges for instruction payloads
- Supports dynamic instruction structures
- Applies tariff-based pricing per component
- Handles validation-based pricing rules
- Computes slice-based fees for divisible vouchers
- Supports wallet-based purchases via Bavix Laravel Wallet
- Uses **Brick Money** for safe and precise monetary handling

------------------------------------------------------------------------

## Key Concepts

### Money Handling

This package uses a dual representation of money:

| Type | Example | Meaning |
|------|--------|--------|
| Decimal (human) | `'5.00'` | ₱5.00 |
| Minor units (system) | `500` | ₱5.00 |

#### Rules

- Use decimal strings when defining prices:
  ```php
  'price' => '5.00'
  ```

- Internally, all values are stored and computed as integer minor units:
  ```php
  500
  ```

- API responses return human-readable decimal values, while also exposing minor units.

This ensures:
- no floating point errors
- consistent financial calculations
- compatibility with wallet systems

------------------------------------------------------------------------

## Installation

```bash
composer require 3neti/instruction
```

Publish config and migrations:

```bash
php artisan vendor:publish --tag=instruction-config
php artisan vendor:publish --tag=instruction-migrations
```

Run migrations:

```bash
php artisan migrate
```

Seed canonical instruction items:

```bash
php artisan db:seed --class="LBHurtado\Instruction\Database\Seeders\InstructionItemSeeder"
```

------------------------------------------------------------------------

## Usage

### Using the Action

```php
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

## Wallet Integration

Instruction items are wallet-capable products.

- Each `InstructionItem` has its own wallet
- Charges can be:
  - estimated (no wallet interaction)
  - executed (via wallet purchase flow)

Example:

```php
$customer->deposit(10000); // ₱100.00

$customer->pay($instructionItem);
```

Revenue is credited to the instruction item's wallet.

------------------------------------------------------------------------

## API

### Endpoint

`POST /api/instruction/v1/estimate`

### Request

```json
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

```json
{
  "success": true,
  "data": {
    "charges": [
      {
        "index": "inputs.fields.email",
        "unit_price": 5,
        "unit_price_minor": 500,
        "quantity": 2,
        "price": 10,
        "price_minor": 1000,
        "currency": "PHP",
        "label": "Email",
        "pay_count": 1
      }
    ],
    "total_amount": 10,
    "total_amount_minor": 1000,
    "total_items_charged": 1,
    "currency": "PHP"
  },
  "meta": {}
}
```

------------------------------------------------------------------------

## Price History

Instruction items support price history tracking.

Each change can be recorded with:
- old price
- new price
- effective date
- optional reason and actor

This enables:
- auditability
- pricing analytics
- future dashboards

------------------------------------------------------------------------

## Design Principles

### 1. Minor Unit Accounting
All monetary values are stored and computed in minor units (e.g., centavos) to ensure precision and eliminate floating-point errors.

### 2. Separation of Concerns
The package is structured into clear layers:

- Models -> persistence and wallet integration
- Services -> pricing and evaluation logic
- DTOs -> serialization and API output

This keeps the system maintainable and testable.

### 3. Human vs System Representation

- Human-facing inputs use decimal strings:
  ```php
  'price' => '5.00'
  ```

- System computations use integers (minor units):
  ```php
  500
  ```

This removes ambiguity and ensures consistent behavior.

### 4. Wallet-First Design

Instruction items are treated as products with wallets:

- Each item can accumulate revenue
- Enables per-instruction accounting
- Supports future dashboards and reconciliation

### 5. Deterministic Pricing

All pricing decisions are:

- explicit
- rule-based
- data-driven

This guarantees consistent results across environments and executions.

### 6. Extensibility

The system is designed to evolve:

- new instruction types
- additional validation rules
- pricing modifiers
- reporting and analytics

without breaking existing behavior.

------------------------------------------------------------------------

## Architecture Diagram (logical)

```text
Client / API / Host App
        |
        v
+-------------------------------+
| EvaluateInstructionCharges    |
| Action                        |
+-------------------------------+
        |
        v
+-------------------------------+
| InstructionCostEvaluator      |
| Service                       |
+-------------------------------+
        |
        +--------------------+
        |                    |
        v                    v
+-------------------+   +----------------------+
| InstructionItem   |   | InstructionSource    |
| Repository        |   | / Customer Adapters  |
+-------------------+   +----------------------+
        |
        v
+-------------------------------+
| InstructionItem Model         |
| - Brick Money                 |
| - Minor-unit price storage    |
| - Bavix ProductInterface      |
| - Wallet-enabled              |
+-------------------------------+
        |
        +--------------------+
        |                    |
        v                    v
+-------------------+   +----------------------+
| instruction_items |   | instruction_item_    |
| table             |   | price_histories      |
+-------------------+   +----------------------+
        |
        v
+-------------------------------+
| API / DTO Layer               |
| - ChargeBreakdownData         |
| - ChargeEstimateData          |
| - decimal display output      |
| - minor-unit metadata         |
+-------------------------------+
```

### Reading the flow

1. A client, controller, or host app calls the action.
2. The action delegates pricing logic to `InstructionCostEvaluator`.
3. The evaluator loads instruction items from the repository.
4. The evaluator inspects the instruction payload and customer context.
5. `InstructionItem` supplies wallet/product pricing behavior and money normalization.
6. Charges are computed in minor units.
7. DTOs serialize the result into API-friendly decimal values, while preserving minor-unit fields.

------------------------------------------------------------------------

## Testing

```bash
composer test
```

------------------------------------------------------------------------

## License

MIT
