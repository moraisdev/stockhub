<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\ShopProducts;
use Illuminate\Http\Request;

class SuppliersAddressController extends Controller
{
    //expected object
    //{
    //    products : [
    //        {
    //            id: [shopify_id]
    //        },
    //        {
    //            id: [shopify_id]
    //        }
    //    ]
    //}
    public function getAddressByProducts(Request $request){
        $return_data = [];

        if(is_array($request->products)){
            foreach($request->products as $product){
                $product = json_decode($product);
                //dd($product);
                if(isset($product->shopify_id)){
                    $db_product = ShopProducts::where('shopify_product_id', $product->shopify_id)->first();
                    if($db_product){
                        $product_array = [
                            'shopify_id' => $db_product->shopify_product_id,
                            'variants' => [],
                            'supplier' => [
                                'address' => [
                                    'zipcode' => $db_product->product->supplier->address->zipcode
                                ]
                            ]
                        ];

                        foreach($db_product->product->variants as $variant){
                            $product_array['variants'][] = [
                                'sku' => $variant->sku,
                                'weight' => $variant->weight_in_grams,
                                'weight_unit' => $variant->weight_unit,
                                'width' => $variant->width,
                                'height' => $variant->height,
                                'depth' => $variant->depth
                            ];
                        }

                        $return_data[] = $product_array;
                    }else{
                        $return_data[] = [
                            'shopify_id' => $product->shopify_id,
                            'error' => true,
                            'message' => 'Produto não encontrado'
                        ];
                    }
                }else{
                    $error = ['status' => 400, 'error_message' => 'The request should contain an array of products called products. Each product should contain a shopify_id field.'];
                    return response($error, 400);
                }
            }

            return $return_data;
        }else{
            $error = ['status' => 400, 'error_message' => 'The request should contain an array of products called products.'];
            return response($error, 400);
        }


    }
}
