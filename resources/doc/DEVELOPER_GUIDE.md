# Instruction Package Developer Guide (x-change Integration)

## Purpose
This guide explains how to integrate the `3neti/instruction` package into the x-change ecosystem.

---

## Architecture Context

x-change uses a modular domain structure:

Client / Vendor UI
        ↓
Pricing (Instruction Package)
        ↓
Voucher Issuance / Wallet / EMI

The instruction package is the pricing engine.

---

## Core Flow

1. Collect instruction payload from UI/vendor
2. Pass payload to instruction evaluator
3. Receive pricing breakdown
4. Validate affordability
5. Proceed with voucher issuance

---

## Step-by-Step Integration

### 1. Install Package

composer require 3neti/instruction

php artisan migrate
php artisan db:seed --class="LBHurtado\Instruction\Database\Seeders\InstructionItemSeeder"

---

### 2. Prepare Customer Adapter

use LBHurtado\Instruction\Support\ArrayChargeableCustomer;

$customer = new ArrayChargeableCustomer([
    'id' => $user->id,
    'email' => $user->email,
]);

---

### 3. Prepare Instruction Payload

use LBHurtado\Instruction\Support\ArrayInstructionSource;

$instructions = new ArrayInstructionSource([
    'count' => 1,
    'inputs' => [
        'fields' => ['email', 'name'],
    ],
    'validation' => [
        'location' => [
            'required' => true,
        ],
    ],
]);

---

### 4. Evaluate Pricing

use LBHurtado\Instruction\Actions\EvaluateInstructionCharges;

$result = app(EvaluateInstructionCharges::class)
    ->handle($customer, $instructions);

---

### 5. Use Result

$total = $result->total_amount;
$charges = $result->charges;

---

## Integration Points in x-change

### Pay Code Issuance

Replace:

InstructionCostEvaluator (host app)

With:

3neti/instruction package

---

### Wallet Validation

Before issuing:

if ($wallet->balance < $result->total_amount) {
    throw new InsufficientBalanceException();
}

---

### API Usage

POST /api/instruction/v1/estimate

Used for:
- frontend preview
- vendor validation
- pricing display

---

## Best Practices

- Always evaluate before issuing voucher
- Never hardcode pricing
- Always seed canonical instruction items
- Treat instruction payload as dynamic

---

## Extending the System

Add new pricing rules by:

1. Adding InstructionItem record
2. Defining index (e.g., inputs.fields.new_field)
3. Setting price and metadata

No code changes required.

---

## Troubleshooting

### No charges returned

- Check instruction payload structure
- Ensure instruction items are seeded

### Wrong pricing

- Verify InstructionItem price
- Check count multiplier

---

## Summary

The instruction package is the single source of truth for pricing in x-change.

Use it consistently across:
- API
- Issuance
- Validation

---

## License

MIT
