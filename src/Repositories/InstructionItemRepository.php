<?php

namespace LBHurtado\Instruction\Repositories;

use Illuminate\Support\Collection;
use LBHurtado\Instruction\Contracts\InstructionItemRepositoryContract;
use LBHurtado\Instruction\Models\InstructionItem;

class InstructionItemRepository implements InstructionItemRepositoryContract
{
    public function all(): Collection
    {
        return InstructionItem::all();
    }

    public function findByIndex(string $index): ?InstructionItem
    {
        return InstructionItem::where('index', $index)->first();
    }

    public function findByIndices(array $indices): Collection
    {
        return InstructionItem::whereIn('index', $indices)->get();
    }

    public function allByType(string $type): Collection
    {
        return InstructionItem::where('type', $type)->get();
    }

    public function totalCharge(array $indices): int|float
    {
        return $this->findByIndices($indices)->sum('price');
    }

    public function descriptionsFor(array $indices): array
    {
        return $this->findByIndices($indices)
            ->mapWithKeys(fn (InstructionItem $item) => [
                $item->index => $item->meta['description'] ?? '',
            ])
            ->toArray();
    }
}
