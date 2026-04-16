<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PpmpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'office_id' => 'required|exists:offices,id',
            'fiscal_year' => 'required|integer|min:2000|max:2100',
            'budget_code' => 'nullable|string|max:50',
            'ppmp_type' => 'required|in:DBM,NON-DBM,LIB',
            'description' => 'nullable|string',
            'president_approved_date' => 'nullable|date',
            'fund_manager' => 'nullable|string|max:255',
            'fund_manager_email' => 'nullable|email|max:255',
            'total_budget' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.unit' => 'nullable|string',
            'items.*.mode_of_procurement' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.q1' => 'nullable|integer|min:0',
            'items.*.q2' => 'nullable|integer|min:0',
            'items.*.q3' => 'nullable|integer|min:0',
            'items.*.q4' => 'nullable|integer|min:0',
        ];

        // Only require status on update
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['status'] = 'required|string|in:draft,approved';
        }

        return $rules;
    }
}
