<?php

namespace App\Http\Requests\Supplier\Products;

use Illuminate\Foundation\Http\FormRequest;

class ProductsCreateRequest extends FormRequest
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
            'title' => 'required|string',
            'description' => 'string',
            'img_source' => 'image|nullable',
            'new_options' => 'array|nullable',
            'new_variants' => 'required|array',
            'new_variants.*.img_source' => 'image|nullable',
            'new_variants.*.sku' => 'required|string',
            'new_variants.*.weight_in_grams' => 'integer|min:0|nullable',
            'new_variants.*.new_options.*' => 'required_with:new_options|nullable',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'img_source' => 'Image',
            'new_options' => 'Options',
            'new_variants' => 'New Variants',
            'new_variants.*.img_source' => 'Image',
            'new_variants.*.sku' => 'SKU',
            'new_variants.*.cost' => 'Cost',
            'new_variants.*.weight_in_grams' => 'Weight (g)',
            'new_variants.*.new_options.*' => 'Variant option',
        ];
    }
}
