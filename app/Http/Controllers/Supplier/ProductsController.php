<?php

namespace App\Http\Controllers\Supplier;

use Auth;
use Storage;
use App\Models\Products;
//use Illuminate\Support\Facades\Storage;
use File;
use Image;
use Illuminate\Support\Facades\Response as Download;

/* Requests */
use App\Models\ErrorLogs;
use App\Models\Categories;
use Illuminate\Support\Str;

/* Services */
use Illuminate\Http\Request;
use App\Models\ProductImages;
use App\Services\BlingService;
use App\Services\FilesService;
use App\Models\ProductVariants;
use App\Services\ReportsService;

use App\Services\ProductsService;

use Illuminate\Support\Facades\DB;
use App\Models\ProductVariantStock;
use App\Services\AliExpressService;
use App\Services\ProductsImportService;
use App\Services\ProductVariantsService;

/* Facades */
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\Supplier\Products\ImportProductsRequest;

use App\Http\Requests\Supplier\Products\ProductsCreateRequest;
use App\Http\Requests\Supplier\Products\ProductsUpdateRequest;
use App\Services\Shop\CsvService;
use Illuminate\Support\Facades\Http;
use DataTables;


class ProductsController extends Controller
{
    public function index(Request $request)
    {
        $supplier = Auth::user();

        $reportsService = new ReportsService($supplier);
        $productsService = new ProductsService($supplier);
        $products = $productsService->get();
        $statistics = $reportsService->getProductsStatistics();
        $products = Products::select('id','title','img_source', 'public')->get();
        $categories = Categories::all();

      
        if ($request->ajax()) {
            $data = Products::select('id')->get();
            return Datatables::of($data)->addIndexColumn()
                ->addColumn('action', function($row){
                    $btn = '<a href="javascript:void(0)" class="btn btn-primary btn-sm">View</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }     

        return view('supplier.products.index');
    }

 public function importProductsBlingJson()
 {
    $supplier = Auth::user();
    $page = 1;


        //faz uma chamada e busca os produtos
        $countProdutosImportados = 0;


        if ($supplier->bling_apikey) {

            while ($page < 50) {

            $blingService = new BlingService();
            $produtosBling = $blingService->importProducts($supplier, $page);


           if ($produtosBling === false){
            echo 'Importação OK';
            break;
           }else {
           // echo 'Aguarde Importando';

            foreach ($produtosBling as $produtoBling) {
                if (Products::where('sku', $produtoBling->produto->codigo)->where('supplier_id', $supplier->id)->count() == 0) {
                    echo $produtoBling->produto->descricao; 
                    Products::create([
                     'supplier_id' => $supplier->id,
                     'sku' => $produtoBling->produto->codigo,
                     'title'=> $produtoBling->produto->descricao,
                     'icms_exemption' => 1,
                     'currency' => 'R$',
                     'description' => strip_tags($produtoBling->produto->descricaoCurta),
                     'public' => 0,
                     'show_in_products_page' => 0,
                     'ncm' => $produtoBling->produto->class_fiscal,
                     'ean_gtin' => $produtoBling->produto->gtin,
                     'products_from' => 'BR'
                 ]);

                 $product = new Products();
                 $resp = $product->where('sku', $produtoBling->produto->codigo)->where('supplier_id', $supplier->id)->first();
                 $resp->hash =  md5(uniqid($resp->id, true));
                 $resp->save();
                }
                
                $altura =0;
                $largura = 0;  
                if(isset($produtoBling->produto->alturaProduto)){
                    $altura =$produtoBling->produto->alturaProduto;
                }

                if(isset($produtoBling->produto->larguraProduto)){
                    $largura = $produtoBling->produto->larguraProduto;  
                }

                
                $product = new Products();
                $resp = $product->where('sku', $produtoBling->produto->codigo)->where('supplier_id', $supplier->id)->first();
                if (ProductVariants::where('product_id', $resp->id)->count() == 0) {
                    $teste = ProductVariants::where('product_id', $resp->id)->count() == 0;
                    $resp2 = $product->where('sku', $produtoBling->produto->codigo)->where('supplier_id', $supplier->id)->first();

                    ProductVariants::create([
                        'product_id' => $resp2->id,
                        'title'=> $resp2->title,
                        'price' => $produtoBling->produto->preco,
                         //   'cost' => $produtoBling->produto->precoCusto,
                        'weight_in_grams' => $produtoBling->produto->pesoBruto * 1000,
                        'height' => $altura,
                        'width' => $largura,
                        'sku' => $produtoBling->produto->codigo

                    ]);


                }
                

                $variant = new ProductVariants();
                $prod = $variant->where('sku', $produtoBling->produto->codigo)->where('product_id', $resp->id)->first();
				
				if($prod){
               if (ProductVariantStock::where('product_variant_id', $prod->id)->count() == 0) {

                   ProductVariantStock::create([
                       'product_variant_id' => $prod->id,
                       'quantity'=> $produtoBling->produto->estoqueAtual,
                   ]);

               }             

               
				}	

                if (isset($produtoBling->produto->imagem)) {
                    foreach ($produtoBling->produto->imagem as $key => $link) {
                    if ($link && property_exists($link, 'link')) {

                        $productimg = new Products();
                        $respimg = $productimg->where('sku', $produtoBling->produto->codigo)->where('id', $resp->id)->first();
                        $respimg->img_source = $link->link;
                        $expiracaoimg =  explode("&",$link->link);
                        $imgvalidacao = explode("=",$link->link);
                        //dd($imgvalidacao);
                        if($expiracaoimg){
                            $dataexpiracao = explode("=", $expiracaoimg[1]);
                            $data = date("Y/m/d",$dataexpiracao[1]); 
                            
                        }
                       
                        $respimg->exp_img_bling = $data;
                        $respimg->valida_img = $imgvalidacao[3];
                        $url = $link->link;
                        $respimg->img_destaque = $link->link;                             
                        $respimg->save();
                        $prodvarimg = new ProductVariants();
                        $respimg2 = $prodvarimg->where('sku', $produtoBling->produto->codigo)->first();
						if($respimg2){
                        $respimg2->img_source = $url;
                        $respimg2->save();
						}
					}
                                            }

                                        }

                                        $product = new Products();
                                        $respimg3 = $product->where('sku', $produtoBling->produto->codigo)->where('id', $resp->id)->first();
                         
                                    if (isset($produtoBling->produto->imagem)) {
                                        foreach ($produtoBling->produto->imagem as $key => $link) { 
                                            $expiracaoimg =  explode("&",$link->link);
                                            $imgvalidacao = explode("=",$link->link);
                                            if($expiracaoimg){
                                                $dataexpiracao = explode("=", $expiracaoimg[1]);
                                                $data = date("Y/m/d",$dataexpiracao[1]); 
                                                $imgvalida = $imgvalidacao[3]; 
                                            }
                                            $verificaimagem = ProductImages::where('img_bling', $link->link)->first();
                                                if (!$verificaimagem) {                                                    
                                                ProductImages::create([
                                                    'product_id' => $respimg3->id,
                                                    'title' => $link->link,
                                                    'src'=> $link->link,
                                                    'img_bling'=> $link->link,
                                                    'img_bling_validade'=> $imgvalida,
                                                    'exp_date_img_bling'=> $data,

                                                ]);

                                            } 
                                    
                                        }
                                }

                                    if (ProductVariants::where('sku', $produtoBling->produto->codigo)->where('product_id', $resp->id)->count() <> 0) {


                                        $vproduct = new ProductVariants();
                                        $respvariants = $vproduct->where('sku', $produtoBling->produto->codigo)->where('product_id', $resp->id)->first();
                                        $respvariants->url_hash =  md5(uniqid($resp->id, true));
                                        $respvariants->save();

                                }
                               // echo $produtoBling->produto->descricao;
                                } // final loop

            }

            $page++;
            //echo $produtoBling->produto->descricao;
           // dd($page);

        }
}

}
    public function importProductsBlingImagem()
    {
        

        $supplier = Auth::user();
        $page = 1;
            //faz uma chamada e busca os produtos
            $countProdutosImportados = 0;
            if ($supplier->bling_apikey) {

                while ($page < 50) {

                $blingService = new BlingService();
                $produtosBling = $blingService->importProducts($supplier, $page);


               if ($produtosBling === false){              
                
                    return true;                         
            
                }else {

                foreach ($produtosBling as $produtoBling) {
                    if (Products::where('sku', $produtoBling->produto->codigo)->count() <> 0) {
                        $product = new Products();
                        $resp = ProductVariants::where('sku', $produtoBling->produto->codigo)->first();

                        if (isset($produtoBling->produto->imagem)) {
                            foreach ($produtoBling->produto->imagem as $key => $link) {
                            if ($link && property_exists($link, 'link')) {                            
                              
                                $url = $link->link;
                                $validacao = CsvService::validarext($url);                            
                                if ($validacao == true) {
                                $productimg = new Products();
                                $urlimg = $link->link;
                                $img4 = substr($urlimg , strrpos($urlimg, '/') + 1);
                                $file = ProductsService::imgpixelmed($urlimg);                           
                                $nomeimg = Storage::disk('digitalocean')->putFile('imagemprojectdrop/'.env('PASTASP'), $file, 'public');   
                                Storage::disk('imgoriginalproduto')->delete($img4);  
    
                                $file2 = ProductsService::imgpixelpeq($urlimg);                           
                                $nomeimg2 = Storage::disk('digitalocean')->putFile('imagemprojectdrop/'.env('PASTASP'), $file2, 'public');   
                                Storage::disk('imgoriginalproduto')->delete($img4);  
    
                                $resp = $productimg->where('sku', $produtoBling->produto->codigo)->first();   
                                $resp->img_source = env('SPACEDIG').env('PASTASP').substr($nomeimg,18,strlen($nomeimg));
                                $resp->img_destaque = env('SPACEDIG').env('PASTASP').'/'.substr($nomeimg2,18,strlen($nomeimg2));
                                $resp->valida_img = $img4;
                                $resp->save();      
                                
                                $prodvarimg = new ProductVariants();
                                $respimg = $prodvarimg->where('sku', $produtoBling->produto->codigo)->first();
                                $respimg->img_source = env('SPACEDIG').env('PASTASP').'/'.substr($nomeimg,18,strlen($nomeimg));
                                $respimg->valida_img = $img4;
                                $respimg->save(); 
                                   }    
                               }
                           }

                        }
                    }
                }
                
            }
            
            
        }   

    }
      


    }

    
   //importacao individual 
    public function importProdutoBlingJson($product_id)
    {
        $supplier = Auth::user();
        $product = new Products();
        $resp = $product->where('id', $product_id)->first();
        

       
       
        if ($supplier->bling_apikey == null){
            return redirect()->route('supplier.products.index')->with('erro', 'Produtos Bling atualizados com sucesso.');

        } 
        if ($supplier->bling_apikey) {



            $blingService = new BlingService();
            $produtosBling = $blingService->importProduto($supplier, $resp);

            foreach ($produtosBling as $produtoBling) {

              
                if (Products::where('sku', $produtoBling->produto->codigo)->where('supplier_id', $supplier->id)->count() <> 0) {
                    $product = new Products();
                    $resp1 = $product->where('sku', $produtoBling->produto->codigo)->where('supplier_id', $supplier->id)->first();
                    $resp1->supplier_id = $supplier->id;
                    $resp1->title = $produtoBling->produto->descricao;
                    $resp1->description = strip_tags($produtoBling->produto->descricaoCurta);
                    $resp1->ncm = $produtoBling->produto->class_fiscal;
                    $resp1->ean_gtin = $produtoBling->produto->gtin;
                    $resp1->save();
                    $resp1 = ProductVariants::where('product_id', $resp->id)->first();

                    if (isset($produtoBling->produto->imagem)) {
                        foreach ($produtoBling->produto->imagem as $key => $link) {
                        if ($link && property_exists($link, 'link')) {                            
                          
                            $url = $link->link;
                            $validacao = CsvService::validarext($url);                            
                            if ($validacao == true) {
                            $productimg = new Products();
                            $urlimg = $link->link;
                            $img4 = substr($urlimg , strrpos($urlimg, '/') + 1);
                         //   $file = ProductsService::imgpixelmed($urlimg);                           
                        //    $nomeimg = Storage::disk('digitalocean')->putFile('imagemprojectdrop/'.env('PASTASP'), $file, 'public');   
                         //   Storage::disk('imgoriginalproduto')->delete($img4);  

                         //   $file2 = ProductsService::imgpixelpeq($urlimg);                           
                         //   $nomeimg2 = Storage::disk('digitalocean')->putFile('imagemprojectdrop/'.env('PASTASP'), $file2, 'public');   
                      //      Storage::disk('imgoriginalproduto')->delete($img4);  

                            $resp2 = $productimg->where('sku', $produtoBling->produto->codigo)->where('id', $resp->id)->first();   
                            $resp2->img_source = $url;
                        //    $resp2->img_destaque = env('SPACEDIG').env('PASTASP').'/'.substr($nomeimg2,18,strlen($nomeimg2));
                            $resp2->valida_img = $img4;
                            $resp2->save();      
                            
                            $prodvarimg = new ProductVariants();
                            $respimg = $prodvarimg->where('sku', $produtoBling->produto->codigo)->where('product_id', $resp->id)->first();
                            $respimg->img_source = $url;
                            $respimg->valida_img = $img4;
                            $respimg->save(); 
                               }    
                           }
                       }

                    }
                }

                if (ProductVariants::where('sku', $produtoBling->produto->codigo)->where('product_id', $resp->id)->count() <> 0) {
                    $resp3 = ProductVariants::where('sku', $produtoBling->produto->codigo)->first();
                    $resp3->title =  $produtoBling->produto->descricao;
                    $resp3->weight_in_grams = $produtoBling->produto->pesoBruto * 1000; //pois no bling ta em kilos e na mawa ta em gramas
                    $resp3->width = $produtoBling->produto->larguraProduto;
                    $resp3->height =$produtoBling->produto->alturaProduto ;
                    $resp3->depth = $produtoBling->produto->profundidadeProduto ;
                    $resp3->save();
                }    

                $respimg =  $product->where('id', $product_id)->first();
               
                if (isset($produtoBling->produto->imagem)) {
                    foreach ($produtoBling->produto->imagem as $key => $link) { 
                        $verificaimagem = ProductImages::where('img_bling', $link->link)->first();
                            if (!$verificaimagem) {                                                    
                            ProductImages::create([
                                'product_id' => $respimg->id,
                                'title' => $link->link,
                                'src'=> $link->link,
                                'img_bling'=> $link->link,
                            ]);

                        } else{                                                                                                
                          
                            $productimg = new ProductImages();      
                            $img = $productimg->where('src', $link->link,)->first();                                             
                            $img->title = $link->link;
                            $img->src = $link->link;
                            $img->save();

                        }    
                
                    }
                }







            }


            return redirect()->route('supplier.products.index')->with('success', 'Produtos Bling atualizados com sucesso.');
           // dd($supplier); 
        }else {
            return redirect()->route('supplier.products.index')->with('erro', 'Produtos Bling atualizados com sucesso.');
           // dd($supplier); 
        }
        //dd($supplier); 
    }

    public function storeBlingProductVariant(Request $request)
    {
        $supplier = Auth::user();
        $variantes = $request->variantes;
        $produto = $request->produto;
        try {
            //faz um loop para adicionar todas as variantes
            //dd($variantes);
            if ($variantes) {
                foreach ($variantes as $key => $variante) {
                    if (strcmp($key, 'produto') == 0) { //quer dizer q é um produto sem variantes
                        $productAux = $variante;
                    } else { //caso contrário é um array de variantes
                        //pega a primeira variante e preenche os dados do produto
                        $productAux = current($variante)['produto'];
                    }
                    $productAux = (object)$productAux;

                    //dd($productAux);

                    //antes de adicionar a variante, verifica se esse produto já não foi adicionado a esse fornecedor, pelo nome
                    $verifyProduto = Products::where('title', 'like', '%' . $produto . '%')
                        ->where('supplier_id', $supplier->id)
                        ->first();

                    //caso não tenha sido adicionado ainda, cria ele
                    if (!$verifyProduto) {
                        $product = new Products();
                        $product->supplier_id = $supplier->id;
                        $product->title = $produto;
                        $product->description = strip_tags($productAux->descricaoCurta);
                        $product->public = 0; //vai como privado de cara
                        $product->img_source = isset($productAux->imagem) && $productAux->imagem[0]['link'] ? $productAux->imagem[0]['link'] : '';
                        $product->save();
                    } else {
                        $product = $verifyProduto;
                    }
                    //senão, só cria as variantes
                    if (strcmp($key, 'produto') == 0) { //quer dizer q é um produto sem variantes
                        if (ProductVariants::where('sku', $productAux->codigo)->get()->count() == 0) {
                            if (isset($productAux->imagem) && count($productAux->imagem) > 0) { //caso tenha imagens para serem adicionadas
                                foreach ($productAux->imagem as $image) {
                                    $name = Str::random(15) . $supplier->id;

                                    $product_image = new ProductImages();
                                    $product_image->product_id = $product->id;
                                    $product_image->title = $name;
                                    $product_image->src = $image['link'];

                                    $product_image->save();
                                }
                            }

                            $product->hash = md5(uniqid($product->id, true));
                            $product->save();

                            //cria uma variante com esse sku
                            $variant = new ProductVariants();

                            $variant->product_id = $product->id;
                            $variant->title = $product->title;
                            $variant->price = $productAux->preco;

                            $variant->weight_in_grams = $productAux->pesoBruto * 1000; //pois no bling ta em kilos e na mawa ta em gramas
                            $variant->width = $productAux->larguraProduto;
                            $variant->height = $productAux->alturaProduto;
                            $variant->depth = $productAux->profundidadeProduto;
                            $variant->sku = $productAux->codigo;
                            $variant->img_source = $product->img_source;

                            if ($variant->save()) {

                                $variant->url_hash = md5(uniqid($variant->id, true));
                                $variant->save();

                                //salva o estoque
                                $stock = ProductVariantStock::firstOrNew(['product_variant_id' => $variant->id]);
                                $stock->quantity = $productAux->estoqueAtual;
                                $stock->save();

                                return response()->json(['msg' => 'Variante salva com sucesso'], 200);
                            }
                        }
                    } else { //caso contrário é um array de variantes
                        //tem que fazer outro foreach para todas as variantes
                        $countVariantsSuccess = 0;

                        foreach ($variantes as $keyMinVariante => $valueMinVariante) {
                            $valueMinVariante = (object)current($valueMinVariante)['produto'];
                            //$valueMinVariante = (object)$valueMinVariante['produto'];
                            if (ProductVariants::where('sku', $valueMinVariante->codigo)->get()->count() == 0) {
                                if (isset($valueMinVariante->imagem) && count($valueMinVariante->imagem) > 0) { //caso tenha imagens para serem adicionadas
                                    foreach ($valueMinVariante->imagem as $image) {
                                        $name = Str::random(15) . $supplier->id;

                                        $product_image = new ProductImages();
                                        $product_image->product_id = $product->id;
                                        $product_image->title = $name;
                                        $product_image->src = $image['link'];

                                        $product_image->save();
                                    }
                                }

                                $product->hash = md5(uniqid($product->id, true));
                                $product->save();

                                //cria uma variante com esse sku
                                $variant = new ProductVariants();

                                $variant->product_id = $product->id;
                                $variant->title = $keyMinVariante;
                                $variant->price = $valueMinVariante->preco;

                                $variant->weight_in_grams = $valueMinVariante->pesoBruto * 1000; //pois no bling ta em kilos e na mawa ta em gramas
                                $variant->width = $valueMinVariante->larguraProduto;
                                $variant->height = $valueMinVariante->alturaProduto;
                                $variant->depth = $valueMinVariante->profundidadeProduto;
                                $variant->sku = $valueMinVariante->codigo;

                                $variant->img_source = isset($valueMinVariante->imagem) ? $valueMinVariante->imagem[0]['link'] : NULL;

                                if ($variant->save()) {
                                    $variant->url_hash = md5(uniqid($variant->id, true));
                                    $variant->save();

                                    //salva o estoque
                                    $stock = ProductVariantStock::firstOrNew(['product_variant_id' => $variant->id]);
                                    $stock->quantity = $valueMinVariante->estoqueAtual;
                                    $stock->save();

                                    $countVariantsSuccess++;
                                }
                            }
                        }

                        if ($countVariantsSuccess == count($variantes)) {
                            return response()->json(['msg' => 'Variantes salvas com sucesso'], 200);
                        }
                    }
                }
            } else {
                return response()->json(['msg' => 'Erro ao importar produtos do Bling. Tente novamente em alguns minutos.'], 400);
            }
        } catch (\Exception $e) {
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            report($e);
            return response()->json(['msg' => 'Erro ao importar produtos do Bling. Tente novamente em alguns minutos.'], 400);
        }
    }

    public function importProductsBling()
    {
        $supplier = Auth::user();
       

        if ($supplier->bling_apikey == NULL){
            return redirect()->route('supplier.products.index')->with('error', 'Erro importar produtos do Bling. Tente novamente em alguns minutos.');

        }

        try {
            //faz uma chamada e busca os produtos
            $countProdutosImportados = 0;
            if ($supplier->bling_apikey) {
                $blingService = new BlingService();
                $produtosBling = $blingService->importProducts($supplier);
                //faz um loop para adicionar todos os produtos
                if ($produtosBling) {
                    foreach ($produtosBling as $produtoBling) {
                        //se o produto ainda está ativo
                        if ($produtoBling->produto->situacao == 'Ativo') {
                            //verifica se o sku ainda não foi adicionado
                            if (ProductVariants::where('sku', $produtoBling->produto->codigo)->get()->count() == 0) {
                                //adiciona o produto
                                //cria o produto
                                $product = new Products();

                                $product->supplier_id = $supplier->id;
                                $product->title = $produtoBling->produto->descricao;
                                //$product->ncm = $request->ncm;
                                //$product->currency = $request->currency;
                                //$product->icms_exemption = $request->icms_exemption;
                                $product->description = strip_tags($produtoBling->produto->descricaoCurta);
                                $product->public = 0; //vai como privado de cara
                                //$product->show_in_products_page = ($request->show_in_products_page == 'on') ? 1 : 0;
                                //$product->shipping_method_china_division = $request->shipping_method_china_division;
                                //$product->packing_weight = $request->packing_weight;
                                //$product->products_from = $request->products_from;
                                $product->img_source = $produtoBling->produto->imagem && $produtoBling->produto->imagem[0]['link'] ? $produtoBling->produto->imagem[0]['link'] : '';
                               
                                $url = $produtoBling->produto->imagem && $produtoBling->produto->imagem[0]['link'] ? $produtoBling->produto->imagem[0]['link'] : '';

                                $contents = file_get_contents($url);
                                $name = substr($url, strrpos($url, '/') + 1);
                              //  Storage::disk('upimgprod')->put($name, $contents);
               
                               // $image_resize = Image::make(public_path().'/imgproduto/'.$name);
                               // $image_resize->pixelate(1);
                               // $image_resize->fit(300);
                              // $image_resize->resize(200, null, function ($constraint) {
                              //     $constraint->aspectRatio();
                              // });
                               
                              // $image_resize->save(public_path('imgproduto/'.$name) , 60);
               
                               $product->hash = md5(uniqid($product->id, true));
                               $product->img_destaque = $url;




                                if ($product->save()) {
                                    if (count($produtoBling->produto->imagem) > 0) { //caso tenha imagens para serem adicionadas
                                        foreach ($produtoBling->produto->imagem as $image) {
                                            $name = Str::random(15) . $supplier->id;

                                            $product_image = new ProductImages();
                                            $product_image->product_id = $product->id;
                                            $product_image->title = $name;
                                            $product_image->src = $image['link'];

                                            $product_image->save();
                                        }
                                    }

                                    $product->hash = md5(uniqid($product->id, true));
                                    $product->save();

                                    //cria uma variante com esse sku
                                    $variant = new ProductVariants();

                                    $variant->product_id = $product->id;
                                    $variant->title = $product->title;
                                    $variant->price = $produtoBling->produto->preco;

                                    $variant->weight_in_grams = $produtoBling->produto->pesoBruto * 1000; //pois no bling ta em kilos e na mawa ta em gramas
                                    $variant->width = $produtoBling->produto->larguraProduto;
                                    $variant->height = $produtoBling->produto->alturaProduto;
                                    $variant->depth = $produtoBling->produto->profundidadeProduto;
                                    $variant->sku = $produtoBling->produto->codigo;

                                    $variant->img_source = $product->img_source;

                                    if ($variant->save()) {
                                        $countProdutosImportados++;

                                        $variant->url_hash = md5(uniqid($variant->id, true));
                                        $variant->save();

                                        //salva o estoque
                                        $stock = ProductVariantStock::firstOrNew(['product_variant_id' => $variant->id]);
                                        $stock->quantity = $produtoBling->produto->estoqueAtual;
                                        $stock->save();
                                    }
                                }
                            }
                        }
                    }

                    if ($countProdutosImportados > 0) {
                        return redirect()->route('supplier.products.index')->with('success', $countProdutosImportados . ' - Produtos importados com sucesso.');
                    } else {
                        return redirect()->route('supplier.products.index')->with('info', 'Nenhum produto importado.');
                    }
                } else {
                    return redirect()->route('supplier.products.index')->with('error', 'Erro importar produtos do Bling. Tente novamente em alguns minutos.');
                }
            }
        } catch (\Exception $e) {
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            report($e);
            return redirect()->route('supplier.products.index')->with('error', 'Erro importar produtos do Bling. Tente novamente em alguns minutos.');
        }
    }

    public function csvInstructions()
    {
        return view('supplier.products.csv_instructions');
    }

    public function downloadCsvModel()
    {
        return Storage::disk('s3')->download('modelo_csv_produto.csv');
    }

    public function show($product_id)
    {
        $supplier = Auth::user();

        
        $productsService = new ProductsService($supplier);

        $product = $productsService->find($product_id);
        
        $categories = ProductsService::getCategories();
        
        

        return view('supplier.products.edit', compact('product', 'categories'));
    }

    public function massiveUpdate (Request $request ){     
        foreach ($request->id as $key => $value) {    
        $supplier = Auth::user();
        $productsService = new ProductsService($supplier);
        $product = $productsService->find($value);     
        $products[] = $productsService->updateMassive($product, $request, $key);
        }

        return redirect()->route('supplier.products.index')->with('success', 'Produtos atualizados com sucesso.');


    }

    public function massiveEdit(Request $request)
    {
        $supplier = Auth::user();
        $productsService = new ProductsService($supplier);
        $products = [];
        $categories = ProductsService::getCategories();
        if (isset($request->check)){
          foreach ($request->check as $value) {
                $products[] = $productsService->find($value);
            }
        }else{
            return redirect()->route('supplier.products.index')->with('error', 'Selecione pelo menos um item.');
        }        
       return view('supplier.products.massive_edit', compact('products', 'categories'));
      

    }

    public function edit($product_id)
    {
        $supplier = Auth::user();

        $productsService = new ProductsService($supplier);

        $product = $productsService->find($product_id);
        $categories = ProductsService::getCategories();

        return view('supplier.products.edit', compact('product', 'categories'));
    }

    public function editRedirectByVariantId($variant_id)
    {
        $supplier = Auth::user();

        $productsService = new ProductsService($supplier);
        $product = $productsService->getByVariantId($variant_id);

        return redirect()->route('supplier.products.edit', [$product->id]);
    }

    public function create()
    {
        
        $categories = ProductsService::getCategories();

        return view('supplier.products.create', compact('categories'));
    }

    public function register(ProductsCreateRequest $request)
    {

        
        $supplier = Auth::user();

        if(!$request->$request->new_variants[0]['sku']){
            return redirect()->back()->with(['erro' => 'O cadastro e necessario um variação principal.']);

         } 
        $product = new Products();       

        $product->category_id = $request->category;
        $product->supplier_id = $supplier->id;
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
        $product->sku = $request->new_variants[0]['sku'];
       
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

            if($request->new_options){
                $options_ids = $this->createOptions($product, $request->new_options);
            }

            if($request->new_variants){
                $this->createVariants($product, $request->new_variants, $options_ids);
            }

            if($request->new_discounts){
                $this->createDiscounts($product, $request->new_discounts);
            }

            return true;
        }else{
            throw new CustomException("Erro ao cadastrar o produto. Tente novamente em alguns minutos.", 500);
        }
    }
    

    public function aliexpressLinkProduct($product_id, $ae_product_id)
    {
        $supplier = Auth::user();

        $productsService = new ProductsService($supplier);
        $product = $productsService->find($product_id);

        $aliexpressService = new AliExpressService($supplier);
        $ae_product = $aliexpressService->findDBProduct($ae_product_id);

        return view('supplier.products.aliexpress_link_products', compact('product', 'ae_product'));
    }

    public function updateVariantsSkus($product_id, Request $request)
    {
        $supplier = Auth::user();
        $aliexpressService = new AliExpressService($supplier);

        $variants = [];

        foreach ($request->options as $variant_id => $options) {
            $options_string = implode(',', $options);

            $variants[] = $aliexpressService->updateVariantSku($variant_id, $options_string);
        }

        return redirect()->route('supplier.products.edit', [$product_id])->with('success', 'SKUs atualizadas com sucesso.');
    }

    public function store(ProductsCreateRequest $request)
    {
       
        $supplier = Auth::user();

        $productsService = new ProductsService($supplier);

        if ($productsService->create($request)) {
            return redirect()->route('supplier.products.index')->with('success', 'Produto cadastrado com sucesso.');
        } else {
            return redirect()->route('supplier.products.index')->with('error', 'Erro ao cadastrar o produto. Tente novamente em alguns minutos.');
        }
    }

    public function update($product_id, ProductsUpdateRequest $request)
    {
        $supplier = Auth::user();

        $productsService = new ProductsService($supplier);

        $product = $productsService->find($product_id);

        if ($productsService->update($product, $request)) {
            return redirect()->route('supplier.products.edit', $product_id)->with('success', 'Produto atualizado com sucesso.');
        } else {
            return redirect()->route('supplier.products.edit', $product_id)->with('error', 'Erro ao atualizar o produto. Tente novamente em alguns minutos.');
        }
    }

    public function publishVariant($product_id, $variant_id)
    {
        $supplier = Auth::user();

        $productsService = new ProductsService($supplier);
        $product = $productsService->find($product_id);

        $productVariantsService = new ProductVariantsService($product);
        $productVariantsService->updateWithArray($variant_id, ['published' => 1]);

        return ['status' => 'success'];
    }

    public function unpublishVariant($product_id, $variant_id)
    {
        $supplier = Auth::user();

        $productsService = new ProductsService($supplier);
        $product = $productsService->find($product_id);

        $productVariantsService = new ProductVariantsService($product);
        $productVariantsService->updateWithArray($variant_id, ['published' => 0]);

        return ['status' => 'success'];
    }

    public function destroy($product_id)
   {
        $supplier = Auth::user();

        $productsService = new ProductsService($supplier);
        $product = $productsService->find($product_id);
       
        $image = ProductImages::where('product_id' , '=' ,$product_id)->get();
        
        foreach($image as $img) 
        {
            
            $res = ProductImages::where('product_id', $img->product_id)->forceDelete();
            
        }
       
        if ($productsService->delete($product)) {
            return redirect()->route('supplier.products.index')->with('success', 'Produto excluído com sucesso OK.' );
        } else {
            return redirect()->route('supplier.products.index')->with('error', 'Erro ao excluir o produto. Tente novamente em alguns minutos.');
        }
        
    }

    public function deleteImage(ProductImages $image)
    {
        if ($image->product->supplier_id != Auth::id()) {
            return response('Unauthorized', 403);
        }

        $image->delete();
    }

    public function importCsv(Request $request)
    {
       

        $supplier = Auth::user();
        $labels = CsvService::storeFilesProd($request);
        $result = CsvService::Importprodutosupplier($labels);
        
        
        

        if (count($result) > 0){
			foreach ($result as $csv_order) {
                
                $especial = env('excelsku');
                if ($especial == 1){
                $sku = str_pad($csv_order['SKU'], 5, '0', STR_PAD_LEFT);
                }else {

                    $sku  = $csv_order['SKU']; 
                   

                }
                $title = $csv_order['Produto'];

                if($title){

                
				
                $publicar = $csv_order['Publico/Privado'];
                $preco = $csv_order['Preço'];
                $qtdestoque = $csv_order['Qtd. em Estoque'];
                if (isset($preco)){
                    
                    $precov = $preco;

                }else {
                    $precov = '0.00';

                }

                if (isset($qtdestoque)){
                    
                    $qtdestoquev = $qtdestoque;

                }else {
                    $qtdestoquev = '0';

                }
                

                if (Products::where('sku', $sku)->where('supplier_id', $supplier->id)->get()->count() == 0) {
                  
                $product = new Products();
                $product->supplier_id = $supplier->id;
                $product->title = $csv_order['Produto'];
                $product->description = strip_tags($csv_order['Descricao']);
                $product->sku = $sku;
                $product->category_id = $csv_order['Categoria'] ? $csv_order['Categoria'] : '';
                $product->public = $publicar;
             //   $product->img_source = $csv_order['URL da Imagem'] && $csv_order['URL da Imagem'] ? $csv_order['URL da Imagem'] : '';
                if($csv_order['Gtin_ean'] ){
                    $product->ean_gtin = $csv_order['Gtin_ean'] ;

                } 

                if($csv_order['Ncm'] ){
                    $product->ncm = $csv_order['Ncm'] ;

                } 

               
                if($product->save()){
                    $product->hash = md5(uniqid($supplier->id, true));                  
                    $product->save();







                }

                 $url = $csv_order['URL da Imagem'];
                         
                 $validacaourl = CsvService::validarext($url);
               
                 if ( $validacaourl == true) {
                    $product->img_destaque =$url;
                    $product->img_source = $url;
                               
                    $product->save();
                   

                    };
                
                                   $variant = new ProductVariants();

                                    $variant->product_id = $product->id;
                                    $variant->title = $product->title;
                                    $variant->price = $precov;
                                    $variant->weight_in_grams = $csv_order['Peso (g)'];
                                    $variant->width = $csv_order['Largura (cm)'];
                                    $variant->height = $csv_order['Altura (cm)'];
                                    $variant->depth = $csv_order['Profundidade (cm)'];
                                    $variant->sku = $sku;
                                    $variant->cost = $csv_order['Custo'] ? $csv_order['Custo'] : '0.00';

                                    $variant->img_source = $product->img_source;

                                    if ($variant->save()) {
                                      
                                        $variant->url_hash = md5(uniqid($variant->id, true));
                                        $variant->save();

                                        //salva o estoque
                                        $stock = ProductVariantStock::firstOrNew(['product_variant_id' => $variant->id]);
                                        $stock->quantity = $csv_order['Qtd. em Estoque'];;
                                        $stock->save();
                                    }

                               // imagem 2 

                                $urlimg2 = $csv_order['URL da Imagem2'];
                                if($urlimg2){
                                   
                                    $validacaoimg2 = CsvService::validarext($urlimg2);
                                    if ($validacaoimg2 == true) {
                                     $product_image1 = new ProductImages();
                                     $product_image1->product_id = $product->id;
                                     $product_image1->title = $urlimg2;
                                     $product_image1->src = $urlimg2;
                                     $product_image1->img_bling = 2;
                                     $product_image1->save();
                                    }
                                } 
                               
                                // imagem 2

                              

                                $urlimg3 = $csv_order['URL da Imagem3'];
                                $validacaoimg3 = CsvService::validarext($urlimg3);
                               if ($validacaoimg3 == true) {
                                $product_image2 = new ProductImages();
                                $product_image2->product_id = $product->id;
                                $product_image2->title = $urlimg3;
                                $product_image2->src = $urlimg3;
                                $product_image2->img_bling = 3;
                                $product_image2->save();
                            }

                            
                                // imagem 3

                                $urlimg4 = $csv_order['URL da Imagem4'];
                               
                                $validacaoimg4 = CsvService::validarext($urlimg4);
                               if ($validacaoimg4 == true) {

                                $product_image3 = new ProductImages();
                                $product_image3->product_id = $product->id;
                                $product_image3->title =  $urlimg4;
                                $product_image3->src = $urlimg4;
                                $product_image3->img_bling = 4;
                                $product_image3->save();

                            }
                                }
                                if (Products::where('sku', $sku)->where('supplier_id', $supplier->id)->get()->count() <> 0) {
                                    $product = Products::where('sku', $sku)->first();
                                    $product->title = $csv_order['Produto'];
                                    $product->description = strip_tags($csv_order['Descricao']);
                                    $product->public = $publicar;
                                    $product->img_source = $csv_order['URL da Imagem'] && $csv_order['URL da Imagem'] ? $csv_order['URL da Imagem'] : '';
                                   
                                    $url = $csv_order['URL da Imagem'];
                         
                                    $validacaourl = CsvService::validarext($url);

                                    if ( $validacaourl == true) {
                                        
                                        $product->img_destaque = $url;
                                        $product->img_source = $url;
                                        $product->hash = md5(uniqid($product->id, true));                  
                                        $product->save();
                                   
                                   
                                    } 
                                    if (ProductVariants::where('sku', $sku)->where('product_id', $product->id)->get()->count() <> 0) {
                                        $variant = ProductVariants::where('sku', $sku)->first();
                                        $variant->title = $product->title;
                                        $variant->price = $precov;
                                        $variant->weight_in_grams = $csv_order['Peso (g)'];
                                        $variant->width = $csv_order['Largura (cm)'];
                                        $variant->height = $csv_order['Altura (cm)'];
                                        $variant->depth = $csv_order['Profundidade (cm)'];   
                                        $variant->cost = $csv_order['Custo'] ? $csv_order['Custo'] : '0.00';                                     
                                        $variant->img_source = $product->img_source;

                                        if ($variant->save()) {
                                            //salva o estoque
                                            if (ProductVariantStock::where('product_variant_id', $variant->id)->get()->count() <> 0) {
                                            $stock = ProductVariantStock::where('product_variant_id', $variant->id)->first();
                                            $stock->quantity = $qtdestoquev;
                                            $stock->save();  
                                            }elseif (ProductVariantStock::where('product_variant_id', $variant->id)->get()->count() == 0) {

                                                $stock = ProductVariantStock::firstOrNew(['product_variant_id' => $variant->id]);
                                                $stock->quantity = $csv_order['Qtd. em Estoque'];;
                                              $stock->save();

                                            }
    
                                        }
                                  
                                          // imagem 2 
                                        $urlimg2 = $csv_order['URL da Imagem2'];
                                        if($urlimg2) {
                                        $validacaoimg2 = CsvService::validarext($urlimg2);
                                        if ($validacaoimg2 == true) {
        
                                        $product_image1 = ProductImages::where('product_id', $product->id)->where('img_bling', 2)->first();
                                        if($product_image1){
                                        
                                        $product_image1->title = $urlimg2;
                                        $product_image1->src = $urlimg2 ;
                                        $product_image1->save();
                                            }
                                        }
                                  
                                  
                                    }

                                    $urlimg3 = $csv_order['URL da Imagem3'];
                                    $validacaoimg3 = CsvService::validarext($urlimg3);
                                   if ($validacaoimg3 == true) {
    
                                    $product_image2 = ProductImages::where('product_id', $product->id)->where('img_bling', 3)->first();
                                    if($product_image2){
                                    $product_image2->title = $urlimg3;
                                    $product_image2->src = $urlimg3;                               
                                    $product_image2->save();
                              
                                        }   
                                    }

                                    // imagem 3

                                $urlimg4 = $csv_order['URL da Imagem4'];
                               
                                $validacaoimg4 = CsvService::validarext($urlimg4);
                               if ($validacaoimg4 == true) {

                                
                                $product_image3 = ProductImages::where('product_id', $product->id)->where('img_bling', 4)->first();
                                if($product_image3){
                                $product_image3->title =$urlimg4;
                                $product_image3->src = $urlimg4;
                                $product_image3->save();
                                }
                            }
                                      

                               
                                // imagem 2
                                }
                              

                               

                            
                                

                                }    
            }  
        }  

        }

       
        return redirect()->back()->with('success', 'Produto Importado com sucesso.');
        

    }


    public function editpublic(Request $request)
    {
        try {  
      $product = Products::where('id' , $request->product_id)->first();
      if($product->public == 0){

      $product->public = 1;
      $product->save();

      }elseif($product->public == 1) {
   
      $product->public = 0;
      $product->save();

      }

     
    
      return redirect()->back()->with('success', 'Produto Publico Atualizado com sucesso.');
            
        } catch (\Exception $e) {
            report($e);
            return redirect()->back()->with('error', 'Erro ao atualizar produto publico.');
    
    }
}


public function tabelas(Request $request)
    {
        $supplier = Auth::user();
       
         
        if ($request->ajax()) {
            $data = Products::select('id','sku','title','img_source', 'public' )->where('supplier_id' , $supplier->id)->get();
            return Datatables::of($data)->addIndexColumn()
            ->addColumn('img_source', function($data){
                if (empty($data->img_source)){
                    return '';
                }
                return '<img src='.$data->img_source.' width="40" height="40"/>';

            })
            ->addColumn('title', function($data){
                
                return substr($data->title, 0,30)."...";

            })
            ->addColumn('public', function($data){
                if ($data->public) {
                   return  '<td class="text-center"><span
                                                        class="badge badge-success">Publico</span>
                                                </td>';


                }else{
                    return '<td class="text-center"><span
                    class="badge badge-info">Privado</span></td>';

                }

                

            })
            ->addColumn('action', function($data){
                $btn = '<a href="javascript:void(0)" onclick="show('.$data->id.')" name="show" id="'.$data->id.'"  class="btn btn-primary btn-circle" role="button"><i
                class="fas fa-eye"></i></a>';
                $btn1 = '<a href="javascript:void(0)" onclick="edit('.$data->id.')" name="edit" id="'.$data->id.'"  class="btn btn-info btn-circle" role="button"><i
                class="fas fa-pencil-alt"></i></a>';
                $btn3 = '<a href="javascript:void(0)" onclick="blingpost('.$data->id.')" name="blingpost" id="'.$data->id.'"  class="btn btn-secondary btn-circle" role="button"><img src="https://th.bing.com/th/id/R.95f031fb946d992f561a6876296e7271?rik=wIEHTb%2fC4eqAdg&pid=ImgRaw&r=0" height ="15" width="15"/>';
             
                
                
                return $btn. $btn1.  $btn3;
            })
            ->rawColumns(['public','img_source','action','btn'])
            
            ->make(true); 
            
        }     

      return view('supplier.products.index');
    }





}