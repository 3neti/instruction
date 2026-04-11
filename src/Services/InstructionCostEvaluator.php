<?php

namespace LBHurtado\Instruction\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use LBHurtado\Instruction\Contracts\ChargeableCustomer;
use LBHurtado\Instruction\Contracts\InstructionItemRepositoryContract;
use LBHurtado\Instruction\Contracts\InstructionSourceContract;
use LBHurtado\Instruction\Data\ChargeBreakdownData;
use LBHurtado\Instruction\Data\ChargeEstimateData;

class InstructionCostEvaluator
{
    protected array $excludedFields = [
        'count',
        'mask',
        'ttl',
        'starts_at',
        'expires_at',
        'cash.slice_fee',
    ];

    public function __construct(
        protected InstructionItemRepositoryContract $repository
    ) {}

    public function evaluate(
        ChargeableCustomer $customer,
        InstructionSourceContract|array $source
    ): Collection {
        $sourceArray = $source instanceof InstructionSourceContract
            ? $source->toArray()
            : $source;

        $charges = collect();
        $items = $this->repository->all();
        $count = data_get($sourceArray, 'count', 1);

        if (config('instruction.debug')) {
            Log::debug('[InstructionCostEvaluator] Starting evaluation', [
                'customer' => $customer->getChargeIdentifier(),
                'instruction_items_count' => $items->count(),
                'count' => $count,
                'source_data' => $sourceArray,
            ]);
        }

        foreach ($items as $item) {
            if (in_array($item->index, $this->excludedFields, true)) {
                continue;
            }

            if (str_starts_with($item->index, 'inputs.fields.')) {
                $fieldName = str_replace('inputs.fields.', '', $item->index);
                $selectedFieldsRaw = data_get($sourceArray, 'inputs.fields', []);

                $selectedFields = collect($selectedFieldsRaw)
                    ->map(function ($field) {
                        if (is_array($field) || is_object($field)) {
                            return collect((array) $field)->values()->first();
                        }

                        return $field;
                    })
                    ->filter()
                    ->toArray();

                $isSelected = in_array(
                    strtoupper($fieldName),
                    array_map('strtoupper', $selectedFields),
                    true
                );

                $value = $isSelected ? $fieldName : null;
            } elseif (str_starts_with($item->index, 'cash.validation.')) {
                $fieldName = str_replace('cash.validation.', '', $item->index);
                $value = data_get($sourceArray, "cash.validation.{$fieldName}");
            } else {
                $value = data_get($sourceArray, $item->index);
            }

            if (str_starts_with($item->index, 'validation.')) {
                if (is_object($value) && method_exists($value, 'toArray')) {
                    $valueArray = $value->toArray();
                } elseif (is_array($value)) {
                    $valueArray = $value;
                } else {
                    $valueArray = [];
                }

                if (isset($valueArray['required'])) {
                    $isEnabled = $valueArray['required'] === true;
                } elseif (isset($valueArray['window']) || isset($valueArray['limit_minutes'])) {
                    $isEnabled = ! empty($valueArray['window']) || ! empty($valueArray['limit_minutes']);
                } else {
                    $isEnabled = false;
                }

                $shouldCharge = $isEnabled && $item->price > 0;
            } else {
                $isTruthyString = is_string($value) && trim($value) !== '';
                $isTruthyBoolean = is_bool($value) && $value === true;
                $isTruthyInteger = is_int($value) && $value > 0;
                $isTruthyFloat = is_float($value) && $value > 0.0;
                $isTruthyObject = (is_array($value) || is_object($value)) && ! empty((array) $value);

                $shouldCharge = (
                    $isTruthyString ||
                    $isTruthyBoolean ||
                    $isTruthyInteger ||
                    $isTruthyFloat ||
                    $isTruthyObject
                ) && $item->price > 0;
            }

            $price = $item->getAmountProduct($customer);

            if ($shouldCharge) {
                $label = $item->meta['label'] ?? $item->name;

                $charges->push(new ChargeBreakdownData(
                    index: $item->index,
                    value: $value,
                    unit_price: $price,
                    quantity: $count,
                    price: $price * $count,
                    currency: $item->currency,
                    label: $label,
                    pay_count: 1,
                ));
            }
        }

        if (data_get($sourceArray, 'cash.slice_mode') !== null) {
            $additionalSlices = match (data_get($sourceArray, 'cash.slice_mode')) {
                'fixed' => data_get($sourceArray, 'cash.slices', 1) - 1,
                'open' => data_get($sourceArray, 'cash.max_slices', 1) - 1,
                default => 0,
            };

            if ($additionalSlices > 0) {
                $sliceFeeItem = $this->repository->findByIndex('cash.slice_fee');

                if ($sliceFeeItem && $sliceFeeItem->price > 0) {
                    $sliceFeePrice = $sliceFeeItem->getAmountProduct($customer);
                    $sliceFeeLabel = $sliceFeeItem->meta['label'] ?? $sliceFeeItem->name;

                    $charges->push(new ChargeBreakdownData(
                        index: 'cash.slice_fee',
                        value: $additionalSlices,
                        unit_price: $sliceFeePrice,
                        quantity: $count,
                        price: $sliceFeePrice * $additionalSlices * $count,
                        currency: $sliceFeeItem->currency,
                        label: $sliceFeeLabel,
                        pay_count: $additionalSlices,
                        slice_count: $additionalSlices,
                    ));
                }
            }
        }

        return $charges;
    }

    public function estimate(
        ChargeableCustomer $customer,
        InstructionSourceContract|array $source
    ): ChargeEstimateData {
        $charges = $this->evaluate($customer, $source);

        return new ChargeEstimateData(
            charges: $charges->all(),
            total_amount: $charges->sum('price'),
            total_items_charged: $charges->count(),
            currency: 'PHP',
        );
    }
}
