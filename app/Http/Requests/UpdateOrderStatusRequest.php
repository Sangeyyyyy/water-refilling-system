<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status' => 'required|in:pending,confirmed,completed,cancelled,rejected,out_for_delivery',
        ];
    }
}
