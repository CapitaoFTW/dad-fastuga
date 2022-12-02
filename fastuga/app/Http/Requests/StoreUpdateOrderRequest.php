<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUpdateOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ticket_number' => 'required|integer|max:99',
            'status' => 'required|in:P,R,D,C',
            'customer_id' => 'required|exists:customers,id',
            'total_price' => 'required|numeric|min:0',
            'total_paid' => 'required|numeric|min:0',
            'total_paid_with_points' => 'required|numeric|min:0',
            'points_gained' => 'required|integer',
            'points_used_to_pay' => 'required|integer',
            'payment_type' => 'nullable|in:VISA,PAYPAL,MBWAY',
            'payment_reference' => 'nullable|string',
            'date' => "required|date",
            'delivered_by' => ['required', Rule::exists('users', 'id')->where('type', 'ED')],
        ];
    }
}
