<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            //product
            'name' => 'required|string|max:2500',
            'description' => 'required|string|max:2500',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'pattern' => 'required|string|max:250',
            'fabric' => 'required|string|max:250',
            'material' => 'required|string',
            'Size_detail'=>'sometimes|file|mimes:png,jpg',

            // Category
            'categories' => 'required',

            // Product images
            'images' => 'sometimes|required|array|min:1',
            'images.*' => 'sometimes|image|mimes:jpeg,png,jpg',

            'main_image' => 'sometimes|file|mimes:png,jpg',
            // Variants
            'variant' => 'required|array|min:1',
            'variant.*.id' => 'sometimes|nullable|exists:variants,id',
            'variant.*.size' => 'required|string|max:10',
            'variant.*.color' => 'required|string|max:20',
            'variant.*.stock' => 'required|integer|min:0',
        ];
    }
}
