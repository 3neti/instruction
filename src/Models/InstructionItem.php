<?php

namespace LBHurtado\Instruction\Models;

use Bavix\Wallet\Interfaces\Customer;
use Bavix\Wallet\Interfaces\ProductInterface;
use Bavix\Wallet\Traits\HasWallet;
use Brick\Money\Money;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use LBHurtado\Instruction\Contracts\ChargeableCustomer;

class InstructionItem extends Model implements ProductInterface
{
    use HasFactory;
    use HasWallet;

    protected $table = 'instruction_items';

    protected $fillable = [
        'name',
        'index',
        'type',
        'price',
        'currency',
        'meta',
        'revenue_destination_type',
        'revenue_destination_id',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    protected $appends = [
        'category',
        'price_minor',
    ];

    public function revenueDestination(): MorphTo
    {
        return $this->morphTo('revenue_destination');
    }

    public function priceHistory(): HasMany
    {
        return $this->hasMany(InstructionItemPriceHistory::class);
    }

    /**
     * Store price as integer minor units.
     *
     * Accepts:
     * - int minor units (e.g. 500)
     * - decimal string (e.g. "5.00")
     * - float/int decimal amount (e.g. 5.00)
     * - Brick\Money\Money
     */
    protected function price(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value === null ? null : (int) $value,
            set: function ($value) {
                if ($value === null || $value === '') {
                    return null;
                }

                if ($value instanceof Money) {
                    return $value->getMinorAmount()->toInt();
                }

                if (is_int($value)) {
                    return $value;
                }

                $currency = $this->currency ?: 'PHP';

                return Money::of((string) $value, $currency)->getMinorAmount()->toInt();
            }
        );
    }

    protected function priceMinor(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->priceAsMoney()->getMinorAmount()->toInt()
        );
    }

    public function getAmountProduct(Customer|ChargeableCustomer $customer): int|string
    {
        $systemEmail = config('instruction.system_user_email');

        $customerEmail = null;

        if ($customer instanceof ChargeableCustomer && method_exists($customer, 'getChargeEmail')) {
            $customerEmail = $customer->getChargeEmail();
        } elseif (method_exists($customer, 'getAttribute')) {
            $customerEmail = $customer->getAttribute('email');
        } elseif (isset($customer->email)) {
            $customerEmail = $customer->email;
        }

        $isSystemUser =
            ($customer instanceof ChargeableCustomer
                && method_exists($customer, 'isSystemUser')
                && $customer->isSystemUser())
            || (
                $this->index === 'cash.amount'
                && $systemEmail
                && $customerEmail
                && strcasecmp((string) $customerEmail, (string) $systemEmail) === 0
            );

        return $isSystemUser ? 0 : $this->priceMinorValue();
    }

    public function getMetaProduct(): ?array
    {
        $title = Arr::get($this->meta, 'label')
            ?? Arr::get($this->meta, 'title')
            ?? $this->name
            ?? ucfirst((string) $this->type);

        $description = Arr::get($this->meta, 'description')
            ?? "Charge for {$this->type} instruction";

        $money = $this->priceAsMoney();

        return [
            'type' => $this->type,
            'title' => $title,
            'description' => $description,
            'currency' => $this->currency,
            'price_minor' => $money->getMinorAmount()->toInt(),
            'price_decimal' => $money->getAmount()->__toString(),
        ];
    }

    public function getUniqueId(): string
    {
        return (string) $this->getKey();
    }

    public function getCategoryAttribute(): string
    {
        return Arr::get($this->meta, 'category', 'other');
    }

    public function priceAsMoney(): Money
    {
        $currency = $this->currency ?: 'PHP';

        $value = $this->getRawOriginal('price');

        if ($value === null) {
            $value = $this->getAttributeFromArray('price');
        }

        if ($value instanceof Money) {
            return $value;
        }

        if (is_int($value)) {
            return Money::ofMinor($value, $currency);
        }

        if (is_string($value) && ctype_digit($value)) {
            return Money::ofMinor((int) $value, $currency);
        }

        if (is_numeric($value)) {
            return Money::of((string) $value, $currency);
        }

        $attributeValue = $this->attributes['price'] ?? null;

        if ($attributeValue instanceof Money) {
            return $attributeValue;
        }

        if (is_int($attributeValue)) {
            return Money::ofMinor($attributeValue, $currency);
        }

        if (is_string($attributeValue) && ctype_digit($attributeValue)) {
            return Money::ofMinor((int) $attributeValue, $currency);
        }

        if (is_numeric($attributeValue)) {
            return Money::of((string) $attributeValue, $currency);
        }

        return Money::ofMinor(0, $currency);
    }

    public static function attributesFromIndex(string $index, array $overrides = []): array
    {
        $segments = explode('.', $index);

        return array_merge([
            'index' => $index,
            'name' => Str::of($index)->afterLast('.')->headline()->toString(),
            'type' => $segments[1] ?? 'general',
            'price' => 0,
            'currency' => 'PHP',
            'meta' => [],
        ], $overrides);
    }

    protected function priceMinorValue(): int
    {
        return $this->priceAsMoney()->getMinorAmount()->toInt();
    }
}