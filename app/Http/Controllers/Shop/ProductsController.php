<?php

namespace App\Http\Controllers\Shop;

use Auth;

/* Services */
use ZipArchive;

use App\Models\Products;

/* Facades */
use App\Models\ShopProducts;
use Illuminate\Http\Request;
use App\Services\Shop\CartxService;
use App\Services\Shop\ShopifyService;
use App\Services\Shop\ProductsService;
use App\Services\Shop\WoocommerceService;
use App\Services\Shop\YampiService;
use Illuminate\Support\Str;
use App\Services\Shop\MercadolivreService;
use App\Models\Mercadolivreapi;
use App\Models\ProductImages;
use App\Services\BlingService;
use App\Models\ProductVariantStock;
use App\Models\YampiApps;
use App\Models\Rating;



class ProductsController extends Controller
{
    public function index(){
        $shop = Auth::guard('shop')->user();

        if($shop->status == 'inactive'){
            return redirect()->back()->with('error', 'O pagamento de sua assinatura está pendente e o acesso ao produtos foi desativado.');
        }

        $productsService = new ProductsService($shop);

        $products = $productsService->paginate(10);
        $statistics = $productsService->getProductsStatistics();
        $apimercadolivreapi = Mercadolivreapi::where('shop_id' ,$shop->id)->first();
        
        return view('shop.products.index', compact('products', 'statistics', 'apimercadolivreapi' , 'shop' ));
    }
    public function getOtherProducts($currentProductId, $limit = 4) {
        return Products::where('id', '!=', $currentProductId)
                       ->take($limit)
                       ->get();
    }
    
    public function rate(Request $request, Products $product)
        {
            $request->validate([
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
            ]);

            $rating = new Rating();
            $rating->shop_id = auth('shop')->id();
            $rating->product_id = $product->id;
            $rating->rating = $request->rating;
            $rating->comment = $request->comment;
            $rating->save();

            return redirect()->back()->with('success', 'Avaliação enviada com sucesso.');
        }

    public function details($product_hash){
        $shop = Auth::guard('shop')->user();
    
        $productsService = new ProductsService($shop);
        $product = $productsService->findByHash($product_hash);
        $product->load('ratings');
    
        // Buscar outros produtos
        $otherProducts = $this->getOtherProducts($product->id);
    
        return view('shop.products.details', compact('product', 'otherProducts'));
    }
    

    public function show($product_id){
        $shop = Auth::guard('shop')->user();

        $productsService = new ProductsService($shop);

        $product = $productsService->find($product_id);

        return view('shop.products.show', compact('product'));
    }

    public function link(Request $request){
        $shop = Auth::guard('shop')->user();

        try {
            $productsService = new ProductsService($shop);
            $product = $productsService->findByHash($request->hash);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Hash inválida.');
        }


        if($productsService->link($product)){
            return redirect()->back()->with('success', 'O produto ' .$product->title.' foi adicionado ao seu carrinho.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu um erro inesperado. Tente novamente em alguns minutos.');
        }
    }

    public function export($product_id){
        set_time_limit(120);
        $shop = Auth::guard('shop')->user();

        if($shop->status == 'inactive'){
            return redirect()->back()->with('error', 'O pagamento de sua assinatura está pendente e a exportação para a sua loja do Shopify foi desativada.');
        }

        $productsService = new ProductsService($shop);
        $product = $productsService->find($product_id);

        $shopify_product = ShopifyService::registerProduct($shop, $product);

        if($shopify_product){
            ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['exported' => 1]);

            return redirect()->back()->with('success', 'Produto exportado para o Shopify com sucesso. Lembre-se de corrigir os valores do produto antes de publica-lo em sua loja.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu um erro inesperado. Tente novamente em alguns minutos.');
        }
    }
        
    public function exportShopifyJson(Request $request){
        set_time_limit(1200);
        $shop = Auth::guard('shop')->user();
        
      
        $productsService = new ProductsService($shop);
        //$product = $productsService->find($request->product_id);
        $product = $productsService->find(1661);
        $shopproduct = ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->first();

        if(!$shopproduct->shopify_product_id || empty($shopproduct->shopify_product_id)){
            $shopify_product = ShopifyService::registerProductJson($shop, $product);     
       
          if(!is_null($shopify_product->id)){
            ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['exported' => 1, 'shopify_product_id' => $shopify_product->id ]);
            return response()->json([
                'msg' => 'Produto exportado para o Shopify com sucesso. Lembre-se de corrigir os valores do produto antes de publica-lo em sua loja.',
                'product_id' => $product->id,
                'shopify_product' => $shopify_product->id], 200);
             }else{
             return response()->json(['error' => 'Aconteceu um erro inesperado. Tente novamente em alguns minutos.'], 400);
             }
        }else{    
            
            return response()->json(['msg' => 'Erro  Produto ja exportado.'], 200);

        }
        
        
    }

    public function exportImagesProductShopifyJson(Request $request){
        set_time_limit(120);
        $shop = Auth::guard('shop')->user();

        $productsService = new ProductsService($shop);
        $product = $productsService->find(6);

        if(ShopifyService::registerImagesProductJson($shop, $request->shopify_product, $product)){
            //ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['exported' => 1]);
            return response()->json([ 'msg' => 'Imagens exportadas com sucesso.'], 200);
        }else{
            return response()->json(['error' => 'Erro ao exportar imagens.'], 400);
        }
    }

    public function exportWoocommerce($product_id){
        set_time_limit(120);
        $shop = Auth::guard('shop')->user();

        if($shop->status == 'inactive'){
            return redirect()->back()->with('error', 'O pagamento de sua assinatura está pendente e a exportação para a sua loja do Shopify foi desativada.');
        }

        $productsService = new ProductsService($shop);
        $product = $productsService->find($product_id);

        $woocommerce_product = WoocommerceService::registerProduct($shop, $product);
       // dd($woocommerce_product);
        if($woocommerce_product){
            ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['exported' => 1]);

            return redirect()->back()->with('success', 'Produto exportado para o Woocommerce com sucesso. Lembre-se de corrigir os valores do produto antes de publica-lo em sua loja.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu um erro inesperado. Tente novamente em alguns minutos.');
        }
    }

    public function exportWoocommerceJson(Request $request){
        $shop = Auth::guard('shop')->user();

        $productsService = new ProductsService($shop);
        $product = $productsService->find($request->product_id);

        $woocommerce_product = WoocommerceService::registerProductJson($shop, $product);

        if($woocommerce_product){
            return response()->json([
                'msg' => 'Produto exportado para o Woocommerce com sucesso. Lembre-se de corrigir os valores do produto antes de publica-lo em sua loja.',
                'product_id' => $product->id,
                'woocommerce_product' => $woocommerce_product], 200);
        }else{
            return response()->json(['error' => 'Aconteceu um erro inesperado. Tente novamente em alguns minutos.'], 400);
        }
    }

    public function exportImagesProductWoocommerceJson(Request $request){
        //set_time_limit(120);
        $shop = Auth::guard('shop')->user();

        $productsService = new ProductsService($shop);
        $product = $productsService->find($request->product_id);

        if(WoocommerceService::registerImagesProductJson($shop, $request->woocommerce_product, $product)){
            //ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['exported' => 1]);
            return response()->json([ 'msg' => 'Imagens exportadas com sucesso.'], 200);
        }else{
            return response()->json(['error' => 'Erro ao exportar imagens.'], 400);
        }
    }

    public function exportCartx($product_id){
        set_time_limit(120);
        $shop = Auth::guard('shop')->user();

        if($shop->status == 'inactive'){
            return redirect()->back()->with('error', 'O pagamento de sua assinatura está pendente e a exportação para a sua loja do Cartx foi desativada.');
        }

        $productsService = new ProductsService($shop);
        $product = $productsService->find($product_id);

        $cartx_product = CartxService::registerProduct($shop, $product);



        if($cartx_product){
            ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['exported_cartx' => 1]);

            return redirect()->back()->with('success', 'Produto exportado para o Cartx com sucesso. Lembre-se de corrigir os valores do produto antes de publica-lo em sua loja.');
        }else{
            return redirect()->back()->with('error', 'Aconteceu um erro inesperado. Tente novamente em alguns minutos.');
        }
    }

    Public function downloadImage($id){
        $produto = Products::with('images')->where('id', $id)->first();

        $zip = new ZipArchive;
        $fileName = 'IMAGENS_'.Str::slug($produto->title, '-').'.zip';
        $flagImgVariants = false;

        if($zip->open(public_path($fileName), ZipArchive::CREATE) == true){ //caso consiga criar o zip corretamente e tenha mais imagens
            if(count($produto->images) > 0){
                foreach($produto->images as $images){
                    //caso seja uma imagem válida
                    if($images->src && @getimagesize($images->src)){
                        # download file
                        $download_file = file_get_contents($images->src);
                        $zip->addFromString(basename($images->src), $download_file);
                        $flagImgVariants = true;
                    }
                }
            }

            if($produto->variants){ //verifica se existe alguma imagem válida entre as variantes, se existir, cria um arquivo com ela(s)
                //pega as imagens das variantes, caso existam
                foreach($produto->variants as $variante){
                    //$file_name = str_replace(env('AWS_URL', 'https://uploads-mawa.s3-sa-east-1.amazonaws.com/'), '', $images->src);
                    //caso seja uma imagem válida
                    if($variante->img_source && @getimagesize($variante->img_source)){
                        # download file
                        $download_file = file_get_contents($variante->img_source);
                        $zip->addFromString(basename($variante->img_source), $download_file);
                        $flagImgVariants = true;
                    }
                }
            }

            if($flagImgVariants){
                $zip->close();
                return response()->download(public_path($fileName))->deleteFileAfterSend(true);
            }else{ //caso não tenha achado nenhuma imagem válida, tem a de perfil
                if($produto->img_source && @getimagesize($produto->img_source)){
                    $filename = Str::slug($produto->title, '-').'.jpg';
                    $tempImage = tempnam(sys_get_temp_dir(), $filename);
                    copy($produto->img_source, $tempImage);
                    return response()->download($tempImage, $filename)->deleteFileAfterSend(true);
                }else{ //caso nem a de perfil exista, retorna erro
                    return redirect()->back()->with('error', 'O fornecedor ainda não cadastrou imagens para este produto.');
                }
            }

        }
    }


    public function exportYampiJson(Request $request){
       
        try {
            $shop = Auth::guard('shop')->user();
           
            $productsService = new ProductsService($shop);
            $product = $productsService->find($request->product_id);

            $yampi_product = YampiService::registerProductJson($shop, $product);

            if($yampi_product){
                return response()->json([
                    'success' => 'Produto exportado para o yampi com sucesso. Lembre-se de corrigir os valores do produto antes de publica-lo em sua loja.',
                    'product_id' => $product->id,
                    'yampi_product' => $yampi_product], 200);

            }else{
                return response()->json(['error' => 'Aconteceu um erro inesperado. Tente novamente em alguns minutos.', $yampi_product], 400);
            }
        } catch (\Throwable $th) {
           return response()->json($th->getMessage());
        }
    }

    public function exportYampiNovo(Request $request){
      
        $shop = Auth::guard('shop')->user();
        $productsService = new ProductsService($shop);
        $prod = $productsService->find($request->idproduct);
       
        $shopprod = YampiApps::where('shop_id', $shop->id)->first();
         if (isset($shopprod->shopify_product_id)) {
            return redirect()->back()->with('error', 'Produto já foi exportado para yampi ');
        }
        if($prod){
            $stock = ProductVariantStock::where('product_variant_id', $prod->id)->first();
            $imagens = ProductImages::where('product_id', $prod->id)->get();
            $yampiService = new YampiService();
            $result = $yampiService->exportProducts($shopprod, $prod, $stock, $imagens);
            $response = explode(' ', $result);

            return redirect()->back()->with('success', 'O produto foi exportado para Yampi.'
                    . ' Atenção: A propagação das imagens nos servidores da Yampi pode ser de até 25 minutos.');
        }
        return redirect()->back()->with('error', 'Entre em contato com o suporte. erro');
    }

    public function exportImagesProductYampiJson(Request $request){
        $shop = Auth::guard('shop')->user();

        $productsService = new ProductsService($shop);
        $product = $productsService->find($request->product_id);

        if(YampiService::registerImagesProductJson($shop, $request->yampi_product, $product)){
            return response()->json([ 'success' => 'Imagens exportadas com sucesso.'], 200);
        }else{
            return response()->json(['error' => 'Erro ao exportar imagens.'], 400);
        }
    }

    public function exportMercadolivre(Request $request){
     
       
       
            $shop = Auth::guard('shop')->user();
            $apimercadolivre = Mercadolivreapi::where('shop_id',$shop->id )->first();
            $productsService = new ProductsService($shop);
            $product = $productsService->find($request->idproduct);
            $shopproduct = ShopProducts::where('shop_id',$shop->id )->where( 'product_id' , $product->id)->first();
            $productimagens = ProductImages::where('product_id', $product->id )->get();
           
            
                
                
           
           $mytime = date('Y-m-d H:i:s');
           $data =  date('d-m-Y');
           
           
            if ($apimercadolivre->token_exp <  $mytime ){
                
                $tokenml = MercadolivreService::getToken($shop, $apimercadolivre );
                $token = Mercadolivreapi::where('shop_id',$shop->id)->first();
                $token->token = $tokenml;
                $token->token_exp = date($mytime, strtotime('+4 Hours'));
                $token->save();
                
            }

            if($shopproduct->ml_product_id <> null){
            $getprodutoml =   MercadolivreService::getProduto($shopproduct, $apimercadolivre , $product  );      
           
            if ($getprodutoml['anuncio']->status == 'closed' ){
                $shopproduct2 = ShopProducts::where('shop_id',$shop->id )->where( 'product_id' , $product->id)->first();
                $shopproduct2->ml_product_id = null;
                $shopproduct2->link_ml_product = null;
                $shopproduct2->seller_id_ml = null;
                $shopproduct2->save(); 
                if ($shopproduct2){
                    return redirect()->back()->with('erro', 'O produto foi cancelado mercadolivre envie o produto novamente mercadolivre.');
    
                }
    
              
            }
           }
    
    
           $product = $productsService->find($request->idproduct);
           $shopproduct = ShopProducts::where('shop_id',$shop->id )->where( 'product_id' , $product->id)->first();
    
           if($shopproduct->ml_product_id <> null){
            $putprodutoml =   MercadolivreService::putProduto($shopproduct, $apimercadolivre , $product  );		
            
             if($putprodutoml){
                $putestoqueml =   MercadolivreService::putEstoque($shopproduct, $apimercadolivre , $product  );
                
                $status = $putestoqueml['status'];  
                if($status == 400){
                    return redirect()->back()->with('success', 'O produto foi atualizado para MercadoLivre mais o estoque nao pode ser atualizado o tipo de anuncio e grátis.');
                   
                  }elseif($putestoqueml['status'] == 200)
                      return redirect()->back()->with('success', 'O produto foi atualizado para MercadoLivre.');
                   
                    }else {
                        return redirect()->back()->with('error', 'O produto não foi exportado entre em contato com o fornecedor.');
                        
    
                    }  
            
           }else{
            
    
            $searchatribbutesml = MercadolivreService::getAtributos($apimercadolivre);
            $searchprodutoml =   MercadolivreService::getBuscaCategoria($shop, $apimercadolivre , $product  );
            $medidas =   MercadolivreService::getmedidas($apimercadolivre);

            if($searchprodutoml){
            
                $postprodutoml =   MercadolivreService::postProduto($shop, $apimercadolivre , $product ,$searchprodutoml ,$productimagens);

				if ($postprodutoml['code'] == 403){
                    $teste = json_encode($postprodutoml['message'], JSON_FORCE_OBJECT);
                    return redirect()->back()->with('error', 'O produto não foi exportado confirme os atributos com o fornecedor.'. $teste);
                    
                }
				
                if ($postprodutoml['code'] == 400){
                    $teste = json_encode($postprodutoml['message'], JSON_FORCE_OBJECT);
                    return redirect()->back()->with('error', 'O produto não foi exportado confirme os atributos com o fornecedor.'. $teste);
                    
                }	
                
                
                if ($postprodutoml['code'] == 401){
                    $tokenml = MercadolivreService::getToken($shop, $apimercadolivre );
                    $token = Mercadolivreapi::where('shop_id',$shop->id)->first();
                    $token->token = $tokenml;
                    $token->token_exp = date($mytime, strtotime('+4 Hours'));
                    $token->save();  
    
    
                    $postprodutoml =   MercadolivreService::postProduto($shop, $apimercadolivre , $product ,$searchprodutoml ,$productimagens);
                }elseif($postprodutoml['code'] == 400){
                    return redirect()->back()->with('error', 'O produto não foi exportado entre em contato com o fornecedor atributos obrigatorio. ');
                    
                }elseif($postprodutoml['code'] == 201){
                    
                 
                    $shopproduct2 = ShopProducts::where('shop_id',$shop->id )->where( 'product_id' , $product->id)->first();
                   
                    $shopproduct2->ml_product_id = $postprodutoml['anuncio']->id;
                    $shopproduct2->link_ml_product = $postprodutoml['anuncio']->permalink;
                    $shopproduct2->seller_id_ml = $postprodutoml['anuncio']->seller_id;
                    $shopproduct2->save();
                  
                    $vendedorml = Mercadolivreapi::where('shop_id',$shop->id)->first();
                    $vendedorml->seller_id_ml =  $postprodutoml['anuncio']->seller_id;        
                    $vendedorml->save();
    
                    if($shopproduct2){
                        $putprodutoml =   MercadolivreService::putProduto($shopproduct2, $apimercadolivre , $product  );
                        $putestoqueml =   MercadolivreService::putEstoque($shopproduct2, $apimercadolivre , $product  );
                        return redirect()->back()->with('success', 'O produto foi exportado para MercadoLivre.');
                    }else {
                        return redirect()->back()->with('error', 'O produto não foi exportado entre em contato com o fornecedor ou tente novamente em alguns instantes.');
                  
                    }    
    
    
    
                }
    
    
            }
    
            
    
           }
            
       
               
            return redirect()->back()->with('error', 'Entre em contato com o suporte. erro');
    
       
       
    }


    public function exportBling(Request $request){
        $shop = Auth::guard('shop')->user();
        $productsService = new ProductsService($shop);
        $prod = $productsService->find($request->idproduct);

        $shopprod = ShopProducts::where('shop_id',$shop->id )->where( 'product_id' , $prod->id)->first();
       
        
       
        if (isset($shopprod->bling_product_id)) {            
            return redirect()->back()->with('error', 'Produto ja foi exportado para bling ');
        }else {

            if ($prod){
                $stock = ProductVariantStock::where('product_variant_id', $prod->id)->first();
                $blingService = new BlingService();
                $imagens = ProductImages::where('product_id', $prod->id)->get();                       
                $produtosBling = $blingService->exportProducts($shop, $prod , $stock, $imagens);
                return redirect()->back()->with('success', 'O produto foi exportado para Bling.');
                }          

        }
        return redirect()->back()->with('error', 'Entre em contato com o suporte. erro');
    }
}

