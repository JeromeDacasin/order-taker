<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateSkuRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                Rule::unique('skus', 'name')->ignore($this->route('sku')),
            ],
            'code' => [
                'required',
                'string',
                Rule::unique('skus', 'code')->ignore($this->route('sku')),
            ],
            'unit_price' => 'required|numeric',
            'image' => 'nullable|string',
            'is_active' => 'nullable|boolean'
        ];
    }
}
