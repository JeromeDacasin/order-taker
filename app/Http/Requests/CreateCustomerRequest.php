<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class CreateCustomerRequest extends FormRequest
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
            'first_name'    => 'required|string',
            'last_name'     => 'required|string',
            'full_name'     => 'unique',
            'mobile_number' => [
                'required',
                'digits:10',
                Rule::unique('customers', 'mobile_number')->ignore($this->route('customer')),
            ],
            'city'          => 'required|string'
        ];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $fullName = $this->first_name . ' ' . $this->last_name;

            $customerId = $this->route('customer');

            $exists = Customer::where('full_name', $fullName)
                ->when($customerId, fn($query) => $query->where('id', '!=', $customerId))
                ->exists();

            if ($exists) {
                $validator->errors()->add('full_name', 'The full name must be unique.');
            }
        });
    }
}
