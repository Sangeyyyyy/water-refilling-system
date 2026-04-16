<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWalkInRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'client_name' => 'nullable|string|max:255',
            'customer_type' => 'required|in:Individual,Office',
            'office_id' => 'nullable|exists:offices,id',
            'other_location' => 'nullable|string|max:255',
            'pr_number' => 'nullable|required_if:customer_type,Office|string|max:255',
            'budget_code' => 'nullable|required_if:customer_type,Office|string|max:255',
            'quantity' => 'required|integer|min:1',
            'pickup_type' => 'required|in:now,later',
            'is_refill' => 'nullable|boolean',
            'missing_caps_count' => 'nullable|integer|min:0',
        ];
    }
}
