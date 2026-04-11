<?php

namespace LBHurtado\Instruction\Actions;

use LBHurtado\Instruction\Contracts\ChargeableCustomer;
use LBHurtado\Instruction\Contracts\InstructionSourceContract;
use LBHurtado\Instruction\Data\ChargeEstimateData;
use LBHurtado\Instruction\Services\InstructionCostEvaluator;

class EvaluateInstructionCharges
{
    public function __construct(
        protected InstructionCostEvaluator $evaluator
    ) {}

    public function handle(
        ChargeableCustomer $customer,
        InstructionSourceContract|array $source
    ): ChargeEstimateData {
        return $this->evaluator->estimate($customer, $source);
    }
}
