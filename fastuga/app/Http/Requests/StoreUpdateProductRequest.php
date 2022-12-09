<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\Product;

class StoreUpdateProductRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255', Rule::unique('products', 'name')->ignore($this->id)],
            'type' => 'required|in:hot dish,cold dish,drink,dessert',
            'photo' => [Rule::requiredIf(is_null(Product::where('name', $this->name)->first())),'base64image'],
            'description' => 'required|min:25',
            'price' => 'required|numeric|between:0,999999.99',
        ];
    }
}
