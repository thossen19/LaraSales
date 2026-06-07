<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'entry_date' => 'required|date',
            'reference_type' => 'nullable|string|max:50',
            'reference_id' => 'nullable|integer',
            'description' => 'required|string|max:1000',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.description' => 'nullable|string|max:500',
            'lines.*.debit_amount' => 'nullable|numeric|min:0',
            'lines.*.credit_amount' => 'nullable|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'entry_date.required' => 'Entry date is required',
            'entry_date.date' => 'Entry date must be a valid date',
            'description.required' => 'Description is required',
            'lines.required' => 'At least 2 line items are required',
            'lines.min' => 'At least 2 line items are required',
            'lines.*.account_id.required' => 'Account is required for each line',
            'lines.*.account_id.exists' => 'Selected account does not exist',
            'lines.*.debit_amount.min' => 'Debit amount must be at least 0',
            'lines.*.credit_amount.min' => 'Credit amount must be at least 0',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($validator->fails()) {
                return;
            }

            $lines = $this->lines;
            $hasValidLines = false;

            foreach ($lines as $line) {
                if (!isset($line['debit_amount']) && !isset($line['credit_amount'])) {
                    $validator->errors()->add('lines', 'Each line must have either a debit or credit amount');
                    return;
                }

                if (isset($line['debit_amount']) && isset($line['credit_amount'])) {
                    $validator->errors()->add('lines', 'Each line can have either debit or credit amount, not both');
                    return;
                }

                if (($line['debit_amount'] ?? 0) > 0 || ($line['credit_amount'] ?? 0) > 0) {
                    $hasValidLines = true;
                }
            }

            if (!$hasValidLines) {
                $validator->errors()->add('lines', 'At least one line must have a positive amount');
            }
        });

        return $validator;
    }
}
