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
            'ticket_number' => 'integer|max:99',
            'status' => 'in:P,R,D,C',
            'customer_id' => 'exists:customers,id',
            'total_price' => 'numeric|min:0',
            'total_paid' => 'numeric|min:0',
            'total_paid_with_points' => 'numeric|min:0',
            'points_gained' => 'integer',
            'points_used_to_pay' => 'integer',
            'payment_type' => 'nullable|in:VISA,PAYPAL,MBWAY',
            'payment_reference' => 'nullable|string',
            'date' => "date",
            'order_items' => "array"
            //'delivered_by' => [Ru, Rule::exists('users', 'id')->where('type', 'ED')],
        ];
    }
}
