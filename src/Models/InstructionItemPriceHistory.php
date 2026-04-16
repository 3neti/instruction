<?php

namespace LBHurtado\Instruction\Models;

use Brick\Money\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InstructionItemPriceHistory extends Model
{
    use HasFactory;

    protected $table = 'instruction_item_price_histories';

    protected $fillable = [
        'instruction_item_id',
        'old_price',
        'new_price',
        'currency',
        'changed_by',
        'reason',
        'effective_at',
    ];

    protected $casts = [
        'old_price' => 'integer',
        'new_price' => 'integer',
        'effective_at' => 'datetime',
    ];

    public function instructionItem(): BelongsTo
    {
        return $this->belongsTo(InstructionItem::class);
    }

    public function oldPriceMoney(): Money
    {
        return Money::ofMinor($this->old_price, $this->currency);
    }

    public function newPriceMoney(): Money
    {
        return Money::ofMinor($this->new_price, $this->currency);
    }
}