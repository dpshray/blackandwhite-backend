<?php

namespace App\Http\Requests\Billing_Information;

use Illuminate\Foundation\Http\FormRequest;

class InformationRequest extends FormRequest
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

            'first_name'=>'required|string',
            'last_name'=>'required|string',
            'email'=>'required|string',
            'state'=>'required|string',
            'city'=>'required|string',
            'address'=>'required|string',
            'contact_number'=>'required|string',
        ];
    }
}
