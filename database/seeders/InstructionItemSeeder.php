<?php

namespace LBHurtado\Instruction\Database\Seeders;

use Illuminate\Database\Seeder;
use LBHurtado\Instruction\Models\InstructionItem;

class InstructionItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'index' => 'cash.amount',
                'name' => 'Cash Amount Fee',
                'type' => 'amount',
                'price' => 20.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Cash Processing Fee',
                    'description' => 'Processing fee for cash instruction',
                    'category' => 'cash',
                ],
            ],
            [
                'index' => 'cash.slice_fee',
                'name' => 'Slice Fee',
                'type' => 'fee',
                'price' => 3.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Slice Fee',
                    'description' => 'Additional fee for extra voucher slices',
                    'category' => 'cash',
                ],
            ],

            [
                'index' => 'inputs.fields.name',
                'name' => 'Name',
                'type' => 'fields',
                'price' => 5.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Name',
                    'description' => 'Charge for name input field',
                    'category' => 'inputs',
                ],
            ],
            [
                'index' => 'inputs.fields.email',
                'name' => 'Email',
                'type' => 'fields',
                'price' => 5.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Email',
                    'description' => 'Charge for email input field',
                    'category' => 'inputs',
                ],
            ],
            [
                'index' => 'inputs.fields.mobile',
                'name' => 'Mobile',
                'type' => 'fields',
                'price' => 5.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Mobile',
                    'description' => 'Charge for mobile input field',
                    'category' => 'inputs',
                ],
            ],
            [
                'index' => 'inputs.fields.address',
                'name' => 'Address',
                'type' => 'fields',
                'price' => 5.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Address',
                    'description' => 'Charge for address input field',
                    'category' => 'inputs',
                ],
            ],
            [
                'index' => 'inputs.fields.birth_date',
                'name' => 'Birth Date',
                'type' => 'fields',
                'price' => 5.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Birth Date',
                    'description' => 'Charge for birth date input field',
                    'category' => 'inputs',
                ],
            ],
            [
                'index' => 'inputs.fields.gross_monthly_income',
                'name' => 'Gross Monthly Income',
                'type' => 'fields',
                'price' => 5.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Gross Monthly Income',
                    'description' => 'Charge for gross monthly income input field',
                    'category' => 'inputs',
                ],
            ],
            [
                'index' => 'inputs.fields.signature',
                'name' => 'Signature',
                'type' => 'fields',
                'price' => 5.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Signature',
                    'description' => 'Charge for signature input field',
                    'category' => 'inputs',
                ],
            ],
            [
                'index' => 'inputs.fields.selfie',
                'name' => 'Selfie',
                'type' => 'fields',
                'price' => 5.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Selfie',
                    'description' => 'Charge for selfie input field',
                    'category' => 'inputs',
                ],
            ],
            [
                'index' => 'inputs.fields.location',
                'name' => 'Location',
                'type' => 'fields',
                'price' => 5.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Location',
                    'description' => 'Charge for location input field',
                    'category' => 'inputs',
                ],
            ],
            [
                'index' => 'inputs.fields.otp',
                'name' => 'OTP',
                'type' => 'fields',
                'price' => 5.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'OTP',
                    'description' => 'Charge for OTP input field',
                    'category' => 'inputs',
                ],
            ],
            [
                'index' => 'inputs.fields.kyc',
                'name' => 'KYC',
                'type' => 'fields',
                'price' => 25.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'KYC',
                    'description' => 'Charge for KYC input field',
                    'category' => 'inputs',
                ],
            ],

            [
                'index' => 'validation.location',
                'name' => 'Location Validation',
                'type' => 'validation',
                'price' => 2.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Location Validation',
                    'description' => 'Charge for location validation',
                    'category' => 'validation',
                ],
            ],
            [
                'index' => 'validation.time',
                'name' => 'Time Validation',
                'type' => 'validation',
                'price' => 2.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Time Validation',
                    'description' => 'Charge for time window or time limit validation',
                    'category' => 'validation',
                ],
            ],
            [
                'index' => 'feedback.email',
                'name' => 'Email Feedback',
                'type' => 'feedback',
                'price' => 1.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Email Feedback',
                    'description' => 'Charge for email feedback',
                    'category' => 'feedback',
                ],
            ],
            [
                'index' => 'feedback.mobile',
                'name' => 'SMS Feedback',
                'type' => 'feedback',
                'price' => 1.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'SMS Feedback',
                    'description' => 'Charge for SMS feedback',
                    'category' => 'feedback',
                ],
            ],
            [
                'index' => 'feedback.webhook',
                'name' => 'Webhook Feedback',
                'type' => 'feedback',
                'price' => 1.00,
                'currency' => 'PHP',
                'meta' => [
                    'label' => 'Webhook Feedback',
                    'description' => 'Charge for webhook feedback',
                    'category' => 'feedback',
                ],
            ],
        ];

        foreach ($items as $attributes) {
            InstructionItem::query()->updateOrCreate(
                ['index' => $attributes['index']],
                $attributes
            );
        }
    }
}
