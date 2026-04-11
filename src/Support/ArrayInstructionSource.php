<?php

namespace LBHurtado\Instruction\Support;

use LBHurtado\Instruction\Contracts\InstructionSourceContract;

class ArrayInstructionSource implements InstructionSourceContract
{
    public function __construct(
        protected array $attributes = []
    ) {}

    public function toArray(): array
    {
        return $this->attributes;
    }
}
