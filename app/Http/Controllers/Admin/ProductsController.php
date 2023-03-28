<?php

namespace App\Http\Controllers\Admin;

use App\Models\Categories;
use App\Models\Products;
use App\Models\Suppliers;
use App\Services\ProductsService;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    public function import($supplier_id, Request $request){
        $categories = Categories::all();

        return view('admin.products.import', compact('categories', 'supplier_id'));
    }

    public function postImport($supplier_id, Request $request){
        set_time_limit(0);
        $supplier = Suppliers::find($supplier_id);
        $products = collect($request->products);

        $new_products = [];

        try {
            foreach($products->where('type', 'product') as $product){
                if($product['identifier'] && strlen($product['identifier']) > 0){
                    $productsService = new ProductsService($supplier);
                    $result = $productsService->importProduct($product);

                    if($result['product']){
                        $new_products[] = $result['product'];
                    }

                    $variants = $products->where('identifier', $product['identifier'])->where('type', 'variant');

                    if($variants->count() > 0){
                        foreach($variants as $variant){
                            $productsService->importVariant($result['product'], $result['options'], $variant);

                        }
                    }else{
                        $productsService->importVariant($result['product'], $result['options'], $product);
                    }
                }
            }
        }catch(\Exception $e){
            report($e);
            return redirect()->route('admin.products.import', $supplier_id)->with('error', 'Algum dado é inválido. Verifique os dados digitados e tente novamente.')->withInput();
        }

        return redirect()->route('admin.products.update_descriptions', $supplier_id)->with('success', 'Produtos importados com sucesso.');
    }

    public function updateDescriptions($supplier_id){
        $products = Products::where('supplier_id', $supplier_id)->get();

        return view('admin.products.update_descriptions', compact('products', 'supplier_id'));
    }

    public function postUpdateDescriptions($supplier_id, Request $request){
        if($request->products){
            foreach($request->products as $id => $description){
                Products::where('id', $id)->update(['description' => $description]);
            }
        }

        return redirect()->back()->with('success', 'Produtos atualizados com sucesso!');
    }

    public function importCSV($supplier_id, Request $request){
        try {
            $path = $request->file('csv_file')->getRealPath();
            $data = array_map('str_getcsv', file($path));

            // SHIFT HEADER OUT OF THE ARRAY
            array_shift($data);

            $headers = [
                'type',
                'identifier',
                'category',
                'title',
                'ncm',
                'description',
                'public',
                'options',
                'options_values',
                'price',
                'sku',
                'weight_in_grams',
                'width',
                'height',
                'depth',
                'images'
            ];

            $csv_products = [];

            foreach($data as $data_line){
                foreach($data_line as $value){
                    $value = ($value == '---') ? '' : $value;

                }

                if(count($data_line) == 16){
                    $csv_products[] = array_combine($headers, $data_line);
                }
            }

            $categories = Categories::all();

            return view('admin.products.import', compact('categories', 'supplier_id', 'csv_products'));
        } catch(\Exception $e){
            report($e);
            return redirect()->back()->with('error', 'Não foi possível importar este arquivo CSV. Verifique se o formato do arquivo está correto.');
        }
    }
}
