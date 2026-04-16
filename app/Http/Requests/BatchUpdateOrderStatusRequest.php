<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BatchUpdateOrderStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:confirmed,completed,cancelled,rejected,out_for_delivery',
        ];
    }
}
