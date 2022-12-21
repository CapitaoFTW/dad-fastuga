<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
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
            'customer_id' => 'nullable|exists:customers,id',
            'customer_points' => ['nullable', Rule::exists('customers', 'points')->where('id', $this->customer_id)],
            'total_price' => 'required|numeric|min:0',
            'points_used_to_pay' => ['nullable', 'integer', 'multiple_of:10', 'min:0', Rule::when($this->customer_points, 'lte:customer_points')],
            'total_paid' => 'required|numeric|min:0',
            'points_gained' => ['required', 'integer'/*, Rule::when($this->points_used_to_pay != 0, 'calculate:total_paid')*/],
            'total_paid_with_points' => ['required', 'numeric', 'min:0'/*, Rule::when($this->points_used_to_pay != 0, 'same_as:points_used_to_pay')*/],
            'payment_type' => 'required|in:VISA,PAYPAL,MBWAY',
            'payment_reference' => ['required', Rule::when($this->payment_type == 'VISA', 'digits:16|doesnt_start_with:0'), Rule::when($this->payment_type == 'MBWAY', 'digits:9|doesnt_start_with:0'), Rule::when($this->payment_type == 'PAYPAL', 'email')],
            'order_items' => 'required|array',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.product_type' => 'required|in:hot dish,cold dish,drink,dessert',
            'order_items.*.product_price' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'total_paid_with_points.same' => "The :attribute must be half of the points used to pay."
        ];
    }
}
