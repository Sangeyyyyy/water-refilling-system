<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $user = auth('web')->user();
        $client = auth('client')->user();
        $isAuth = $user || $client;

        return [
            'first_name' => $isAuth ? 'nullable|string|max:255' : 'required|string|max:255',
            'last_name' => $isAuth ? 'nullable|string|max:255' : 'required|string|max:255',
            'customer_type' => 'required|string|in:Individual,Office',
            'pr_number' => 'nullable|string|max:255',
            'ppmp_id' => 'required_if:customer_type,Office|nullable|exists:ppmps,id',
            'budget_code' => 'required_if:customer_type,Office|nullable|string|max:255',
            'office_id' => $isAuth ? 'nullable|exists:offices,id' : 'required_without:other_location|nullable|exists:offices,id',
            'other_location' => 'required_without:office_id|nullable|string|max:255',
            'contact_number' => $isAuth ? 'nullable|string|max:20' : 'required|string|max:20',
            'quantity' => 'required|integer|min:2',
            'delivery_date' => 'required|date',
            'is_refill' => 'nullable|boolean',
            'container_ownership' => 'required_if:is_refill,1|in:dnsc,personal',
            'missing_caps_count' => 'nullable|integer|min:0',
        ];
    }
}
