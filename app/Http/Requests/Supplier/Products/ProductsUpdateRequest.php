<?php

namespace App\Http\Requests\Supplier\Products;

use Illuminate\Foundation\Http\FormRequest;

class ProductsUpdateRequest extends FormRequest
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
            'description' => 'required|string',
            'img_source' => 'image|nullable',
            'options' => 'array|nullable',
            'new_options' => 'array|nullable',
            'variants' => 'required_without:new_variants|array',
            'new_variants' => 'array',
            'variants.*.img_source' => 'image|nullable',
            'variants.*.sku' => 'required|string',
            'variants.*.weight_in_grams' => 'integer|min:0|nullable',
            'variants.*.options.*' => 'required_with:options|nullable',
            'variants.*.new_options.*' => 'required_with:new_options|nullable',
            'new_variants.*.img_source' => 'image|nullable',
            'new_variants.*.sku' => 'required|string',
            'new_variants.*.weight_in_grams' => 'integer|min:0|nullable',
            'new_variants.*.options.*' => 'required_with:options|nullable',
            'new_variants.*.new_options.*' => 'required_with:new_options|nullable',
        ];
    }

    public function attributes()
    {
        return [
            'title' => 'Title',
            'description' => 'Description',
            'img_source' => 'Image',
            'options' => 'Options',
            'new_options' => 'Options',
            'variants' => 'Variantes',
            'variants.*.img_source' => 'Variant image',
            'variants.*.sku' => 'Variant SKU',
            'variants.*.cost' => 'Variant cost',
            'variants.*.weight_in_grams' => 'Variant weight (g)',
            'variants.*.options.*' => 'Variant options',
            'variants.*.new_options.*' => 'Variant options',
            'new_variants' => 'New variants',
            'new_variants.*.img_source' => 'New variant image',
            'new_variants.*.sku' => 'New variant SKU',
            'new_variants.*.cost' => 'New variant cost',
            'new_variants.*.weight_in_grams' => 'New variant weight (g)',
            'new_variants.*.options.*' => 'New variant options',
            'new_variants.*.new_options.*' => 'New variant options',
        ];
    }
}
