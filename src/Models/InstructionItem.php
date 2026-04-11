<?php

namespace LBHurtado\Instruction\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LBHurtado\Instruction\Contracts\ChargeableCustomer;

class InstructionItem extends Model
{
    use HasFactory;

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
        'price' => 'float',
    ];

    public function revenueDestination()
    {
        return $this->morphTo('revenue_destination');
    }

    public function getAmountProduct(ChargeableCustomer $customer): int|float|string
    {
        if ($this->index === 'cash.amount') {
            $systemEmail = config('instruction.system_user_email');

            if ($systemEmail && $customer->getChargeEmail() === $systemEmail) {
                return 0;
            }
        }

        return $this->price;
    }

    public function getMetaProduct(): ?array
    {
        return [
            'type' => $this->type,
            'title' => $this->meta['label'] ?? $this->meta['title'] ?? ucfirst($this->type),
            'description' => $this->meta['description'] ?? "Charge for {$this->type} instruction",
        ];
    }

    public function getCategoryAttribute(): string
    {
        return $this->meta['category'] ?? 'other';
    }

    public static function attributesFromIndex(string $index, array $overrides = []): array
    {
        return array_merge([
            'index' => $index,
            'name' => Str::of($index)->afterLast('.')->headline()->toString(),
            'type' => Str::of($index)->explode('.')[1] ?? 'general',
            'price' => 0,
            'currency' => 'PHP',
            'meta' => [],
        ], $overrides);
    }
}
