<?php

namespace App\Http\Requests\Contact;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
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
            //
            'firstname'=>'required|string|max:50',
            'lastname'=>'required|string|max:50',
            'email'=>'required|string|email',
            'phone'=>'required|string',
            'message'=>'required|string|max:250'
        ];
    }
}
