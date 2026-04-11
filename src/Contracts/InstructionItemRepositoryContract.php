<?php

namespace LBHurtado\Instruction\Contracts;

use Illuminate\Support\Collection;
use LBHurtado\Instruction\Models\InstructionItem;

interface InstructionItemRepositoryContract
{
    public function all(): Collection;

    public function findByIndex(string $index): ?InstructionItem;

    public function findByIndices(array $indices): Collection;

    public function allByType(string $type): Collection;

    public function totalCharge(array $indices): int|float;

    public function descriptionsFor(array $indices): array;
}
