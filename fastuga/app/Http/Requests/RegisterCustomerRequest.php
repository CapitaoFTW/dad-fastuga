<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterCustomerRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:3|confirmed',
            'password_confirmation' => 'required|same:password',
            'phone' => 'required|digits:9|starts_with:2,9|unique:customers,phone',
            'nif' => 'required|digits:9|unique:customers,nif',
            'payment_type' => 'required|in:VISA,PAYPAL,MBWAY',
            'payment_reference' => ['required', Rule::when($this->payment_type == 'VISA', 'digits:16|doesnt_start_with:0'), Rule::when($this->payment_type == 'MBWAY', 'digits:9|doesnt_start_with:0'), Rule::when($this->payment_type == 'PAYPAL', 'email')],
        ];
    }
}
