<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class UpdateUserRequest extends FormRequest
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
            'id' => 'required',
            'name' => 'required|string|max:255',
            'email' => ['required','email', Rule::unique('users', 'email')->ignore($this->id)],
            'photo' => 'base64image',
            'customer.phone' => ['digits:9', 'starts_with:+,2,9', Rule::unique('customers', 'phone')->ignore($this->id, 'user_id')],
            'customer.nif' => ['digits:9', Rule::unique('customers', 'nif')->ignore($this->id, 'user_id')],
            'customer.default_payment_type' => 'in:VISA,PAYPAL,MBWAY',
            //'customer.default_payment_reference' => [Rule::when($this->customer['default_payment_type']== 'VISA' && $this->customer != null, 'digits:16|doesnt_start_with:0'), Rule::when($this->customer['default_payment_type'] == 'MBWAY' && $this->customer != null, 'digits:9|doesnt_start_with:0'), Rule::when($this->customer['default_payment_type'] == 'PAYPAL' && $this->customer != null, 'email')],
        ];
    }
}
