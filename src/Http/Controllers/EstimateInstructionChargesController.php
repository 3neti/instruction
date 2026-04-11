<?php

namespace LBHurtado\Instruction\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use LBHurtado\Instruction\Actions\EvaluateInstructionCharges;
use LBHurtado\Instruction\Http\Requests\EstimateInstructionChargesRequest;
use LBHurtado\Instruction\Support\ArrayChargeableCustomer;
use LBHurtado\Instruction\Support\ArrayInstructionSource;

class EstimateInstructionChargesController extends Controller
{
    public function __invoke(
        EstimateInstructionChargesRequest $request,
        EvaluateInstructionCharges $action
    ): JsonResponse {
        $customer = new ArrayChargeableCustomer(
            $request->input('customer', [])
        );

        $source = new ArrayInstructionSource(
            $request->input('instructions', [])
        );

        $result = $action->handle($customer, $source);

        return response()->json([
            'success' => true,
            'data' => $result->toArray(),
            'meta' => [],
        ]);
    }
}
