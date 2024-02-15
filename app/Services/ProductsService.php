<?php

namespace App\Services;

use App\Exceptions\CustomException;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/* Models */
use App\Models\Categories;
use App\Models\Suppliers;
use App\Models\Products;
use App\Models\ProductImages;
use App\Models\ProductOptions;
use App\Models\ProductVariants;
use App\Models\ProductVariantOptionsValues;
use App\Models\AliexpressProducts;
use App\Models\ShopProducts;
use App\Models\MatchSku;

/* Services */
use App\Services\ProductOptionsService;
use File;
use Image;

class ProductsService
{

    public $supplier;

    public function __construct(Suppliers $supplier)
    {
        $this->supplier = $supplier;

        if (!$this->supplier) {
            throw new CustomException("Aconteceu um erro inesperado. Tente novamente em alguns minutos.", 404);
        }
    }

    public static function getCategories()
    {
        return Categories::all();
    }

    public function get()
    {
        return Products::where('supplier_id', $this->supplier->id)->get();
    }

    public function getByVariantId($variant_id)
    {
        $variant = ProductVariants::find($variant_id);

        $product = Products::where('supplier_id', $this->supplier->id)->find($variant->product_id);

        if (!$product) {
            throw new CustomException("Produto não encontrado.", 404);
        }

        return $product;
    }

    public function paginate(int $quantity, $name = null, $category_id = null)
    {
        $products = Products::where('supplier_id', $this->supplier->id);

        if ($name) {
            $products->where('title', 'LIKE', '%' . $name . '%');
        }

        if ($category_id) {
            $products->where('category_id', $category_id);
        }

        return $products->paginate($quantity);
    }

    public function find(int $id)
    {
        $product = Products::where('supplier_id', $this->supplier->id)->with(['variants', 'options'])->find($id);


        if (!$product) {
            throw new CustomException("Produto não encontrado.", 404);
        }

        return $product;
    }

    public function createFromAliExpressProduct($ae_product_id)
    {
        $ae_product = AliexpressProducts::find($ae_product_id);

        $product = new Products();

        $product->supplier_id = $this->supplier->id;
        $product->title = $ae_product->title;
        $product->description = $ae_product->description;

        $product->save();

        $product->hash = md5(uniqid($product->id, true));
        $product->save();

        $options = $ae_product->options->pluck('option');

        if ($options) {
            $options_ids = $this->createOptions($product, $options);
        }

        if ($ae_product->variants) {
            $this->createVariantsFromAliexpress($product, $ae_product->variants, $options_ids);
        }

        return $product;
    }

    public function create(Request $request)
    {
        if (isset($request->new_variants) && count($request->new_variants) > 0) {
            foreach ($request->new_variants as $variantVerify) {
                $variantVerified = ProductVariants::where('sku', $variantVerify['sku'])
                    ->first();
                if ($variantVerified) {
                    throw new CustomException("Erro sku " . $variantVerify['sku'] . " Sku Cadastrada com sucesso.", 500);
                }
            }
        }

        $product = new Products();

        $product->category_id = $request->category;
        $product->supplier_id = $this->supplier->id;
        $product->title = $request->title;
        $product->ncm = $request->ncm;
        $product->ean_gtin = $request->ean_gtin;
        $product->product_brand = $request->product_brand;
        $product->sku_secondary = $request->new_variants[0]['sku_secondary'];
        $product->currency = $request->currency;
        $product->icms_exemption = $request->icms_exemption;
        $product->description = $request->description;
        $product->public = ($request->public == 'on') ? 1 : 0;
        $product->show_in_products_page = ($request->show_in_products_page == 'on') ? 1 : 0;
        $product->shipping_method_china_division = $request->shipping_method_china_division;
        $product->packing_weight = $request->packing_weight;
        $product->products_from = $request->products_from;
        $product->sku = $request->new_variants[0]['sku'];
        $product->general_information = $request->input('general_information');

        if ($request->hasFile('img_source')) {
            $imageData = file_get_contents($request->img_source->getRealPath());
            $encodedData = base64_encode($imageData);
            $product->img_source_data = $encodedData;
        }

        if ($product->save()) {
            if ($request->hasFile('images')) {
                foreach ($request->images as $image) {
                    $product_image = new ProductImages();
                    $product_image->product_id = $product->id;
                    $product_image->title = $image->getClientOriginalName();

                    $imageData = file_get_contents($image->getRealPath());
                    $encodedData = base64_encode($imageData);
                    $product_image->image_data = $encodedData;

                    $product_image->save();
                }
            }

            $product->hash = md5(uniqid($product->id, true));
            $product->save();

            $options_ids = null;

            if ($request->new_options) {
                $options_ids = $this->createOptions($product, $request->new_options);
            }

            if ($request->new_variants) {
                $this->createVariants($product, $request->new_variants, $options_ids);
            }

            if ($request->new_discounts) {
                $this->createDiscounts($product, $request->new_discounts);
            }

            return true;
        } else {
            throw new CustomException("Erro ao cadastrar o produto. Tente novamente em alguns minutos.", 500);
        }
    }

    public function updateMassive($product, $request, $i = 0)
    {

        $product->category_id = $request->category[$i] ?: NULL;
        $product->title = $request->title[$i];
        $product->ncm = $request->ncm[$i];
        $product->product_brand = $request->product_brand[$i];
        $product->ean_gtin = $request->ean_gtin[$i];
        $product->currency = $request->currency[$i] ? 1 : 0;
        $product->icms_exemption = $request->icms_exemption[$i];
        $product->description = $request->description[$i];
        $product->public = $request->public[$i] ? 1 : 0;
        $product->show_in_products_page = $request->show_in_products_page[$i] ? 1 : 0;
        $product->products_from = $request->products_from[$i];

        if (Auth::guard('admin')->id() != NULL) {
            $product->ignore_percentage_on_tax = $request->ignore_percentage_on_tax ? NULL : NULL;
        }

        if ($product->save()) {

            $options_ids = null;

            $this->updateOptions($product, $request->options);

            if ($request->new_options) {
                $options_ids = $this->createOptions($product, $request->new_options);
            }

            if ($request->variants) {
                $this->updateVariants($product, $request->variants, $options_ids);
            }

            $this->updateDiscounts($product, $request->discounts);

            if ($request->new_variants) {
                $this->createVariants($product, $request->new_variants, $options_ids);
            }

            if ($request->new_discounts) {
                $this->createDiscounts($product, $request->new_discounts);
            }
            $shopProducts = ShopProducts::where('product_id', $product->id)->limit(100)->get();
            foreach ($shopProducts as $shopProduct) {
            }
            return $product;
        } else {
            throw new CustomException("Erro ao atualizar o produto. Tente novamente em alguns minutos.", 500);
        }
    }

    public function update($product, Request $request)
    {
        if (isset($request->variants) && count($request->variants) > 0) {
            foreach ($request->variants as $variantVerify) {
                $variantVerified = ProductVariants::where('sku', $variantVerify['sku'])
                    ->where('product_id', '!=', $product->id)
                    ->first();
                if ($variantVerified) {
                    throw new CustomException("Erro sku " . $variantVerify['sku'] . " já utilizada por outro fornecedor.", 500);
                }
            }
        }
        if (isset($request->variants) && count($request->variants) > 0) {
            foreach ($request->variants as $variant) {
                if (isset($variant['sku'])) {
                    $existingSkuSecondaries = MatchSku::where('sku', $variant['sku'])->pluck('sku_secondary')->toArray();

                    if (isset($variant['sku_secondary']) && is_array($variant['sku_secondary'])) {
                        foreach ($variant['sku_secondary'] as $skuSecondary) {
                            $matchSku = MatchSku::where('sku', $variant['sku'])->where('sku_secondary', $skuSecondary)->first();
                            if (!$matchSku) {
                                MatchSku::create([
                                    'sku' => $variant['sku'],
                                    'sku_secondary' => $skuSecondary
                                ]);
                            }
                            Products::where('sku', $skuSecondary)->update(['public' => 1]);

                            if (($key = array_search($skuSecondary, $existingSkuSecondaries)) !== false) {
                                unset($existingSkuSecondaries[$key]);
                            }
                        }
                    }

                    foreach ($existingSkuSecondaries as $skuSecondary) {
                        MatchSku::where('sku', $variant['sku'])->where('sku_secondary', $skuSecondary)->delete();
                        Products::where('sku', $skuSecondary)->update(['public' => 0]);
                    }
                }
            }
        }

        $product->category_id = $request->category;
        $product->title = $request->title;
        $product->ncm = $request->ncm;
        $product->ean_gtin = $request->ean_gtin;
        $product->currency = $request->currency;
        $product->icms_exemption = $request->icms_exemption;
        $product->description = $request->description;
        $product->public = ($request->public == 'on') ? 1 : 0;
        $product->show_in_products_page = ($request->show_in_products_page == 'on') ? 1 : 0;
        $product->shipping_method_china_division = $request->shipping_method_china_division;
        $product->packing_weight = $request->packing_weight;
        $product->products_from = $request->products_from;
        $product->product_brand = $request->product_brand;
        $product->sku_secondary = $request->sku_secondary;
        $product->general_information = $request->input('general_information');

        if (Auth::guard('admin')->id() != null) {
            $product->ignore_percentage_on_tax = $request->ignore_percentage_on_tax;
        }

        if ($request->hasFile('img_source')) {
            $imageData = file_get_contents($request->img_source->getRealPath());
            $encodedData = base64_encode($imageData);
            $product->img_source_data = $encodedData;
        }

        if ($product->save()) {
            if ($request->hasFile('images')) {
                foreach ($request->images as $image) {
                    $product_image = new ProductImages();
                    $product_image->product_id = $product->id;
                    $product_image->title = $image->getClientOriginalName();

                    $imageData = file_get_contents($image->getRealPath());
                    $encodedData = base64_encode($imageData);
                    $product_image->image_data = $encodedData;

                    $product_image->save();
                }
            }
        }

        if ($product->save()) {
            $options_ids = null;

            $this->updateOptions($product, $request->options);

            if ($request->new_options) {
                $options_ids = $this->createOptions($product, $request->new_options);
            }

            $this->updateVariants($product, $request->variants, $options_ids);
            $this->updateDiscounts($product, $request->discounts);

            if ($request->new_variants) {
                $this->createVariants($product, $request->new_variants, $options_ids);
            }

            if ($request->new_discounts) {
                $this->createDiscounts($product, $request->new_discounts);
            }

            $shopProducts = ShopProducts::where('product_id', $product->id)->limit(100)->get();
            foreach ($shopProducts as $shopProduct) {
            }


            return true;
        } else {
            throw new CustomException("Erro ao atualizar o produto. Tente novamente em alguns minutos.", 500);
        }
    }

    public function delete($product)
    {
        if ($product->delete()) {
            ProductVariants::where('product_id', $product->id)->delete();

            return true;
        } else {
            throw new CustomException("Erro ao excluir o produto. Tente novamente em alguns minutos.", 500);
        }
    }

    public function createOptions($product, $new_options)
    {
        $optionsService = new ProductOptionsService($product);

        $options_ids = collect();

        foreach ($new_options as $option_key => $name) {
            $option = $optionsService->create($name);

            $options_ids->push(['option_id' => $option->id, 'option_key' => $option_key]);
        }

        return $options_ids;
    }

    public function updateOptions($product, $options)
    {
        $optionsService = new ProductOptionsService($product);

        $updated_options_ids = [];

        if ($options) {
            foreach ($options as $option_id => $name) {
                $optionsService->update($option_id, $name);

                $updated_options_ids[] = $option_id;
            }
        }

        $optionsService->deleteWhereNotIn($updated_options_ids);
    }

    public function updateVariants($product, $variants, $options_ids)
    {
        $variantsService = new ProductVariantsService($product);

        $updated_variants_ids = [];

        if ($variants) {
            foreach ($variants as $variant_id => $variant_fields) {
                if (ProductVariants::where('product_id', $product->id)->find($variant_id)) {
                    $variantsService->update($variant_id, (object) $variant_fields, $options_ids);
                    $updated_variants_ids[] = $variant_id;
                }
                ;
            }
        }

        $variantsService->deleteWhereNotIn($updated_variants_ids);
    }

    public function createVariants($product, $new_variants, $options_ids)
    {
        $variantsService = new ProductVariantsService($product);

        foreach ($new_variants as $fields) {
            $variantsService->create((object) $fields, $options_ids);
        }
    }

    public function createDiscounts($product, $new_discounts)
    {
        $discountsService = new ProductDiscountsService($product);

        foreach ($new_discounts as $fields) { //cria todos os descontos daquele produto
            $discountsService->create((object) $fields);
        }
    }

    public function updateDiscounts($product, $discounts)
    {
        $discountsService = new ProductDiscountsService($product);

        $updated_discounts_ids = [];

        if ($discounts) {
            foreach ($discounts as $discount_id => $discount_fields) {
                $discountsService->update($discount_id, (object) $discount_fields);

                $updated_discounts_ids[] = $discount_id;
            }
        }

        $discountsService->deleteWhereNotIn($updated_discounts_ids);
    }

    public function createVariantsFromAliexpress($product, $variants, $options_ids)
    {
        $variantsService = new ProductVariantsService($product);

        foreach ($variants as $variant) {
            $variantsService->createFromAliexpress($variant, $options_ids);
        }
    }

    public function importProduct($data)
    {
        $product = new Products();

        $product->category_id = $data['category_id'];
        $product->supplier_id = $this->supplier->id;
        $product->title = $data['title'];
        $product->ncm = $data['ncm'];
        $product->description = $data['description'];
        $product->product_brand = $data['product_brand'];
        $product->public = $data['public'];

        $product->save();

        $product->hash = md5(uniqid($product->id, true));
        $product->save();

        $options = explode(';', $data['options']);

        if (!empty($options)) {
            $options_keys = [];

            foreach ($options as $key => $option) {
                $new_option = ProductOptions::firstOrCreate(['product_id' => $product->id, 'name' => $option]);

                $options_keys[$key] = $new_option->id;
            }
        }

        return ['product' => $product, 'options' => $options_keys];
    }

    public function importVariant($new_product, $options, $data)
    {
        if (isset($data['sku'])) {
            $variant = new ProductVariants();

            $variant->product_id = $new_product->id;
            $variant->title = $data['title'];
            $variant->price = $data['price'];
            $variant->cost = 0;
            $variant->requires_shipping = 1;
            $variant->weight_in_grams = isset($data['weight_in_grams']) ? $data['weight_in_grams'] : 0;
            $variant->weight_unit = 'g';
            $variant->width = isset($data['width']) ? $data['width'] : '';
            $variant->height = isset($data['height']) ? $data['height'] : '';
            $variant->depth = isset($data['depth']) ? $data['depth'] : '';
            $variant->sku = isset($data['sku']) ? $data['sku'] : '';
            $variant->published = 1;

            $variant->save();

            if ($options && !empty($options)) {
                $values = explode(';', $data['options_values']);

                foreach ($values as $key => $value) {
                    if (isset($options[$key])) {
                        ProductVariantOptionsValues::create(['product_variant_id' => $variant->id, 'product_option_id' => $options[$key], 'value' => $value]);
                    }
                }
            }

            return $variant;

        } else {
            return false;
        }
    }


    public static function imgpixelmed($urlimg)
    {


        $img4 = substr($urlimg, strrpos($urlimg, '/') + 1);
        File::copy($urlimg, public_path('imgoriginalproduto/' . $img4));
        $image_resize4 = Image::make(public_path('/imgoriginalproduto/' . $img4));
        $image_resize4->resize(400, null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $file = public_path('imgoriginalproduto/' . $img4);

        return $file;


    }

    public static function imgpixelpeq($urlimg)
    {


        $img4 = substr($urlimg, strrpos($urlimg, '/') + 1);
        File::copy($urlimg, public_path('imgoriginalproduto/' . $img4));
        $image_resize4 = Image::make(public_path('/imgoriginalproduto/' . $img4));
        $image_resize4->resize(100, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        $image_resize4->save(public_path('imgproduto/' . $img4), 60);
        $file = public_path('imgproduto/' . $img4);


        return $file;


    }
}
