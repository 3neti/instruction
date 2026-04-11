<?php

namespace LBHurtado\Instruction\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EstimateInstructionChargesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer' => ['required', 'array'],
            'customer.id' => ['nullable'],
            'customer.email' => ['nullable', 'email'],

            'instructions' => ['required', 'array'],
            'instructions.count' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer.required' => 'The customer payload is required.',
            'customer.array' => 'The customer payload must be an object.',
            'customer.email.email' => 'The customer email must be a valid email address.',
            'instructions.required' => 'The instructions payload is required.',
            'instructions.array' => 'The instructions payload must be an object.',
            'instructions.count.integer' => 'The instructions count must be an integer.',
            'instructions.count.min' => 'The instructions count must be at least 1.',
        ];
    }
}
