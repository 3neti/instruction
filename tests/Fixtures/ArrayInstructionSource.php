<?php

namespace LBHurtado\Instruction\Tests\Fixtures;

use LBHurtado\Instruction\Contracts\InstructionSourceContract;

class ArrayInstructionSource implements InstructionSourceContract
{
    public function __construct(
        protected array $data = []
    ) {}

    public function toArray(): array
    {
        return $this->data;
    }
}
