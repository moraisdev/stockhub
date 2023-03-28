<?php

namespace App\Services\Shop;

use App\Models\Orders;
use App\Models\Customers;
use App\Models\ErrorLogs;
use App\Models\Suppliers;
use App\Models\OrderItems;
use App\Models\ShopProducts;
use App\Imports\OrdersImport;
use App\Models\OrderShippings;
use App\Models\ProductVariants;
use App\Models\CustomerAddresses;
use App\Models\ShippingLabel;
use App\Services\CorreiosService;
use Illuminate\Support\Facades\DB;
use App\Services\MelhorEnvioService;
use Illuminate\Support\Facades\Auth;
use App\Services\TotalExpressService;
use App\Services\ChinaShippingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Imports\Importproduto;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\Excel;

class CsvService{

    public static function storeFiles($request){
        //salva a planilha primeiro
        if($request->hasFile('file')){
            $shop = Auth::guard('shop')->user();

            $namePlan = Str::random(15). '.' . $request->file->extension();

            $pathPlan = $request->file->storeAs('shipping_labels/'.$shop->id, $namePlan, 'public');

            $newShippingLabel = ShippingLabel::create(['url_planilha' => $pathPlan]);
            //caso tenha enviado as etiquetas, salva tbm
            if($request->hasFile('file_shipping_labels')){
                $upload = $request->file('file_shipping_labels');
                $nameShippingLabels = Str::random(15). '.' . $request->file_shipping_labels->extension();

                $upload->move(public_path('etiqueta'),$nameShippingLabels);

                $newShippingLabel->url_labels = $nameShippingLabels;                
                $newShippingLabel->save();
            }

            return $newShippingLabel;
        }
    }

    public static function importCsvOrder($request){
     
        $collection = (new OrdersImport)->toCollection($request->file('file'));
       
        $uniqueItems = $collection[0]->unique('Código Pedido');       
        $array = [];     
        foreach ($uniqueItems as $unique){
            $unique->put('item', $collection[0]->where('Código Pedido', $unique['Código Pedido']));  
            $array[] = $unique ;             
        }
        return $array;
    }
   
    public static function registerOrder($shop, $csv_order, $labels)
    {
        $shop = Auth::guard('shop')->user();
        $shop_id = $shop->id;
        
        //$customer_id = DB::Select('select id from customers where shop_id = ' . $shop_id . ' and email = "' . $csv_order['Email'] . '"');
        if(!$csv_order['Código Pedido']) return " pedido sem identificação (Código do pedido). ";
        if(!$csv_order['Cliente']) return " cliente sem nome. ";
      //  if(!$csv_order['CPF']) return $csv_order["Cliente"]." não possui CPF. ";

        try {
            if ($shop->status == 'inactive') {
                return false;
            }

            // if (!$csv_order['E-mail']) {
            //     return false;
            // }
            
            if (!self::checkOrderItems($csv_order)) {
                return false;
            }

            $newExternalId = bin2hex(random_bytes(30));
            
            $order = Orders::create(['shop_id' => $shop->id, 'external_id' => $newExternalId ]); //gerar um random

            $order->external_service = 'planilha';
            $order->name = $csv_order['Código Pedido'];           
            $order->email = isset($csv_order['E-mail']) ? $csv_order['E-mail'] : '';
            $order->external_price = isset($csv_order['Total']) ? $csv_order['Total'] : 0.0;
            // $order->external_usd_price = $csv_order->total / 5.34;
            $order->landing_site = NULL;
            $order->status = 'pending';
            $order->external_created_at = isset($csv_order['Paid at']) ? date('Y-m-d h:i:s', strtotime($csv_order['Paid at'])) : NULL;
            $order->tracking_number  = isset($csv_order['Rastreio']) ? $csv_order['Rastreio'] : '';
            if (!$order->save()) {
                return null;
            }
            $items = self::registerItems($order, $csv_order['item']);
            $customer = self::registerCustomer($shop, $csv_order);
            $shipping = self::registerShipping($order, $items['items'], $customer, $shop);
           // dd($shipping);
            //quando o valor do frete estiver zerado o os dados de endereço não forem informados, quer dizer que é por etiqueta, então salva o id da etiqua na ordem
            if( $shipping['total_shipping_amount'] == 0
                && !$customer->address->address1 && !$customer->address->zipcode && !$customer->address->city
                /*&& $labels->url_labels*/){
                $order->shipping_label_id = $labels->id;
            }

            if (!$customer || !$items || !$shipping || count($items) == 0) {
                $order->external_id = null;
                $order->save();
                $order->delete();

                return null;
            }

            $order->customer_id = $customer->id;
            $order->items_amount = $items['total_amount'];
            $order->shipping_amount = $shipping['total_shipping_amount'];
            $order->amount = $items['total_amount'] + $shipping['total_shipping_amount'];
            $order->save();

            return 'true';
        } catch (\Exception $e) {
            Log::error($e);
            //dd($e);
             report($e);
             ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    /* Verify if theres a registered product in the csv order */
    public static function checkOrderItems($csv_line_items)
    {

        try {
            //dd($csv_line_items['item']);
            foreach ($csv_line_items['item'] as $csv_item) {
                //caso seja um kit, retira os dados kit-unidades antes de fazer essa verificação
                $arrSkuCheck = explode("-", $csv_item['SKU']);

                if (count($arrSkuCheck) > 2 && $arrSkuCheck[1]) { //caso tenha a segunda posição no vetor
                    $unidadesSkuCheck = intval($arrSkuCheck[1]);
                } else {
                    $unidadesSkuCheck = 0;
                }

                if (strtoupper($arrSkuCheck[0]) == "KIT" && $unidadesSkuCheck > 0) {
                    $stringSku = "";
                    for ($i = 2; $i < count($arrSkuCheck); $i++) {
                        $stringSku .= $arrSkuCheck[$i] . ($i < count($arrSkuCheck) - 1 ? "-" : ""); //monta a string novamente
                    }

                    $variant = ProductVariants::where('sku', $stringSku)->first();
                } else {
                    $variant = ProductVariants::where('sku', $csv_item['SKU'])->first();
                }

                if ($variant) {
                    return true;
                }
            }

            return false;
        } catch (\Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    public static function registerItems($order, $csv_line_items){
        try {
            $items = array();
            $total_amount = 0;
            //dd($csv_line_items);
            foreach ($csv_line_items as $csv_item) {
                //dd($csv_item);
                //antes, verifica se é um kit, caso seja, multiplica pela quantidade que indica na string
                $arrSku = explode("-", $csv_item['SKU']);
                //a string deve ter o padrão kit-unidades-skuitem
                if(count($arrSku) > 2 && $arrSku[1]){ //caso tenha a segunda posição no vetor
                    $unidadesSku = intval($arrSku[1]);
                }else{
                    $unidadesSku = 0;
                }                 
                if(strtoupper($arrSku[0]) == "KIT" && $unidadesSku > 0){
                    $stringSku = "";
                    for($i = 2; $i < count($arrSku); $i++){
                        $stringSku.=$arrSku[$i].($i < count($arrSku) - 1 ? "-" : ""); //monta a string novamente
                    }                   

                    $variant = ProductVariants::where('sku', $stringSku)->first();

                    $aplicatedDiscount = 0; //desconto aplicado
                    $orderItemDiscount = NULL; //salva o desconto usado no item (caso exista)

                    //caso tenha o token do bling e caso não aceite pedidos com estoque zerado, sai fora
                    if($variant->product->supplier->bling_apikey && $variant->product->supplier->empty_stock_bling == 'nao'){
                        continue;
                    }

                    if($variant){
                        $variant_id = $variant->id;
                        $charge_or_not = 1;
                        $amount = ($variant->price * $csv_item['Quantidade'] * $unidadesSku); //salva x vezes a quantidade de items do kit
                        $amount = $amount - ($amount * ($aplicatedDiscount/100));
                        $total_amount += $amount;
                    }else{
                        continue;
                    }

                    $item = new OrderItems();
                    $item->order_id = $order->id;
                    $item->product_variant_id = $variant_id;
                    $item->external_service = 'planilha';
                    $item->external_product_id = isset($csv_item['Id']) ? $csv_item['Id'] : NULL;
                    $item->external_variant_id =  NULL;
                    $item->sku = $stringSku; //sku sem a tag
                    $item->title = $variant && $variant->title ? $variant->title : '';
                    $item->quantity = $csv_item['Quantidade'] * $unidadesSku;
                    $item->amount = $amount;
                    $item->external_price = isset($csv_item['Lineitem price']) ? $csv_item['Lineitem price'] : 0.0;
                    $item->charge = $charge_or_not;

                    //dd($item);

                    $item->save();

                    if($orderItemDiscount){ //caso haja desconto, salva o item o OrderItem
                        $orderItemDiscount->order_item_id = $item->id;
                        $orderItemDiscount->save();
                    }

                    array_push($items, $item);
                }else{ //caso não seja um kit faz com o sku normal
                    $variant = ProductVariants::where('sku', $csv_item['SKU'])->first();                   
                    
                    $aplicatedDiscount = 0; //desconto aplicado
                    $orderItemDiscount = NULL; //salva o desconto usado no item (caso exista)
                    //dd($csv_item['SKU']);
                    if($variant){
                        $variant_id = $variant->id;
                        $charge_or_not = 1;                                                                        
                        $amount = ($variant->price * $csv_item['Quantidade']);
                        $amount = $amount - ($amount * ($aplicatedDiscount/100));

                        $total_amount += $amount;
                    }else{
                        continue;
                    }
                    
                    $item = new OrderItems();                

                    $item->order_id = $order->id;
                    $item->product_variant_id = $variant_id;
                    $item->external_service = 'planilha';
                    $item->external_product_id = isset($csv_item['Id']) ? $csv_item['Id'] : null;
                    $item->external_variant_id =  null;
                    $item->sku = $csv_item['SKU'];
                    $item->title = $variant && $variant->title ? $variant->title : '';
                    $item->quantity = $csv_item['Quantidade'];
                    $item->amount = $amount;
                    $item->external_price = isset($csv_item['Lineitem price']) ? $csv_item['Lineitem price'] : 0.0;
                    $item->charge = $charge_or_not;
                    $item->save();

                    if($orderItemDiscount){ //caso haja desconto, salva o item o OrderItem
                        $orderItemDiscount->order_item_id = $item->id;
                        $orderItemDiscount->save();
                    }                  

                    array_push($items, $item);
                }                
            }        
            return ['items' => $items, 'total_amount' => $total_amount];
        } catch(\Exception $e){
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
	}

    

    public static function registerShipping($order, $items, $customer, $shop)
    {

        try {
            $shipping_items = [];
            $total_shipping_amount = 0;

            foreach ($items as $item) {
                if ($item->variant && $item->variant->product) {
                    $shipping_items[$item->variant->product->supplier_id][] = $item;
                }
            }

            $shippings = [];
            foreach ($shipping_items as $supplier_id => $items) {
                $supplier = Suppliers::find($supplier_id);

                if ($supplier) {

                    $shipping_amount = self::calculateShipping($items, $supplier, $customer);
                  //  dd($shipping_amount);
                    if ($supplier->shipping_fixed_fee && $supplier->shipping_fixed_fee > 0) {
                        $shipping_amount += $supplier->shipping_fixed_fee;
                    }

                    
                    $shipping = new OrderShippings();

                    $shipping->supplier_id = $supplier->id;
                    $shipping->order_id = $order->id;

                    if ($supplier->id == 56) { //caso seja a s2m2 adiciona os 4% de taxa no frete do produto também
                        $shipping_amount = $shipping_amount * 1.05;
                    }

                    if($supplier->id == 43){ //caso seja a ksimports adiciona os R$ 5,00 de taxa no frete
                        $shipping_amount = $shipping_amount + 5.00;
                    }

                    $shipping->amount = $shipping_amount;

                    $shipping->save();

                    $shippings[] = $shipping;

                    $total_shipping_amount += $shipping_amount;
                }
            }

            return ['total_shipping_amount' => $total_shipping_amount, 'shippings' => $shippings];
        } catch (\Exception $e) {
            //dd($e);
            // report($e);
            // ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    //por conta da melhor envios agora tbm é necessário os dados do lojista
    public static function calculateShipping($items, $supplier, $customer, $shop = NULL, $order = NULL)
    {
        try {
            //dd($customer);
            //caso seja o pessoal da s2m2, verifica o método de envio dos china
            // if ($supplier->id == 56) {
            //     $chinaShippingService = new ChinaShippingService();

            //     $address = $customer->address;

            //     $products = $chinaShippingService->prepareOrderProducts($items);

            //     $chinaShippingService->setToZipcode($address->zipcode);
            //     $chinaShippingService->calcBoxWeight($products);

            //     $valor = $chinaShippingService->getShippingPrice();

            //     if ($valor && $valor > 0) {
            //         return $valor;
            //     }
            // }

            if ($supplier->shipping_method == 'correios' && $supplier->correios_settings) {

                $address = $customer->address;
                $products = CorreiosService::prepareOrderProducts($items);

                $correiosService = new CorreiosService();

                $correiosService->setFromZipcode($supplier->address->zipcode);
                $correiosService->setToZipcode($address->zipcode);
                $correiosService->calcBoxSize($products);

                $result = $correiosService->getShippingPrices($supplier);

                if ($result->pac && $result->pac > 0) {
                    return $result->pac * ($supplier->correios_settings->percentage / 100);
                }
            }

            if ($supplier->shipping_method == 'total_express' && $supplier->total_express_settings) {

                $address = $customer->address;
                $products = TotalExpressService::prepareOrderProducts($items);

                $totalExpressService = new TotalExpressService($supplier->total_express_settings);

                $totalExpressService->setToZipcode($address->zipcode);
                $totalExpressService->calcBoxSize($products);

                $valor = $totalExpressService->getValorServico();

                if ($valor && $valor > 0) {
                    return $valor;
                }
            }

            
           
            if ($supplier->shipping_method == 'melhor_envio' && isset($customer->cpf) && $customer->cpf != '') {
                //só calcula o frete com a melhor envio se o usuário tiver CPF

                $address = $customer->address;

                $melhorEnvioService = new MelhorEnvioService();
                $melhorEnvioService->setFromZipcode($supplier->address->zipcode);
                $melhorEnvioService->setToZipcode($address->zipcode);
                $melhorEnvioService->prepareOrderProducts($items);

                //só retorna o valor da cotacao
                $valor = $melhorEnvioService->quoteFreightMinValue();

                if ($valor && $valor > 0) {
                    return $valor;
                }
                //dd($customer->cpf);
            }
            //dd($customer->cpf);
        } catch (\Exception $e) {
            report($e);
            //dd($e);
        }
       // dd($customer->cpf);
        return 0;
    }

    public static function registerCustomer($shop, $csv_customer)
    {

        try {
            $customer = Customers::create(['shop_id' => $shop->id]);
            
            $customer->external_service = 'planilha';
            $customer->first_name = strtok($csv_customer['Cliente'], ' ');
            //$customer->last_name = strstr($csv_customer['Billing Name'], ' ');
            $customer->email = isset($csv_customer['E-mail']) ? $csv_customer['E-mail'] : '';
            $customer->total_spent = isset($csv_customer['Taxes']) ? $csv_customer['Taxes'] : 0.0;
			$cpf = isset($csv_customer['CPF']) ? $csv_customer['CPF'] : '';
			$countcpf = strlen($cpf);
			
			
			
			if ($countcpf == 10){
				$customer->cpf =  '0'.$cpf;
			}	
			
            

            if (!$customer->save()) {
                return null;
            }
            //dd($customer);
            $address = self::registerCustomerAddress($customer, $csv_customer);

            if (!$address) {
                return null;
            }

            return $customer;
        } catch (\Exception $e) {           
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    public static function registerCustomerAddress($customer, $csv_customer)
    {
        try {
            $address = CustomerAddresses::create(['customer_id' => $customer->id, 'address1' => $csv_customer['Logradouro']]);

            $address->name = $csv_customer['Cliente'];
            //$address->company = $csv_customer['Billing Company'];
            $address->address1 = $csv_customer['Logradouro'].($csv_customer['Número'] ?  ', '.$csv_customer['Número'] : '');
            $address->address2 = $csv_customer['Bairro'];
            $address->city = $csv_customer['Cidade'];
            $address->province = $csv_customer['UF'];
            $address->country = 'Brasil';
            
            //$address->phone = $csv_customer['Billing Phone'];
            $address->province_code =  $csv_customer['UF'];
            $address->country_code = 'BR';
			
			$cep = $csv_customer['CEP'];
			$cep = trim($cep);
			$cep = str_replace('-', '', $cep);
			$cep = str_replace('.', '', $cep);
            $address->zipcode = $cep;
			
			
            //dd($address);

            if (!$address->save()) {
                $customer->delete();
                return null;
            }

            return $address;
        } catch (\Exception $e) {          
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    public static function registerProduct($shop, $product)
    {
        try {
            if ($shop->status == 'inactive') {
                return false;
            }
            //dd($product);
            $data = (object)[
                // 'id' => $product->id,
                'name' => $product->title,
                'description' => $product->description,
                'type' => 'simple',
                'options' => [],
                'attributes' => [],
                'variations' => [],
                'sku' => $product->variants[0]->sku,
                'regular_price' => $product->variants[0]->price,
                'weight' => $product->variants[0]->weight,
                'images' => []

            ];

            if ($product->img_source) {
                $data->images[] = (object)['src' => $product->img_source];
            }

            foreach ($product->images as $image) {
                $data->images[] = (object)['src' => $image->src];
            }


            foreach ($product->options as $option) {
                $values = [];

                foreach ($product->variants as $variant) {
                    foreach ($variant->options_values->where('product_option_id', $option->id) as $option_value) {
                        $values[] = $option_value->value;
                    }
                }

                $data->options[] = (object)[
                    'name' => $option->name,
                    'values' => [$option->name]
                ];
            }

            foreach ($product->variants as $variant) {
                $i = 1;
                $variant_data = [];
                $attributes_data = [];

                if ($variant->options_values) { //caso o produto tenha opções
                    foreach ($variant->options_values as $option_value) {
                        $variant_data["description"] = $option_value->value;
                        $i++;
                    }
                }

                $variant_data['sale_price'] = $variant->price;
                $variant_data['sku'] =  $variant->sku;
                $variant_data['weight'] = ($variant->weight_in_grams != null) ? $variant->weight_in_grams : 0;

                $attributes_data['name'] = 'color';
                $attributes_data['option'] = $option_value->value;

                $data->attributes[] = (object)$attributes_data;
                $data->variations[] = (object)$variant_data;

                if (count($data->attributes) > 0) {
                    $data->type = 'variable';
                }


                if ($variant->img_source) {
                    $data->images[] = (object)['src' => $variant->img_source];
                }
            }

            $client = new \GuzzleHttp\Client([
                // Base URI is used with relative requests
                'base_uri' => $shop->csv_app->domain,
            ]);

            // $response = $client->request('post', '/wp-json/wc/v3/products', [
            //     'headers' => [
            //         "Authorization" => "Basic ". base64_encode($shop->csv_app->app_key.':'.$shop->csv_app->app_password)
            //     ],
            //     'verify' => false, //only needed if you are facing SSL certificate issue
            //     'json' => $data,
            // ]);

            // if($response->getStatusCode() == 200 || $response->getStatusCode() == 201){

            //     $csv_product = json_decode($response->getBody());

            // }


            //     $client = new \GuzzleHttp\Client([
            //         // Base URI is used with relative requests
            //         'base_uri' => $shop->csv_app->domain,
            //     ]);
            //     $response3 = $client->request('GET', '/wp-json/wc/v3/products?limit=1', [
            //         'headers' => [
            //             "Authorization" => "Basic ". base64_encode($shop->csv_app->app_key.':'.$shop->csv_app->app_password)
            //         ],
            //         'verify' => false, //only needed if you are facing SSL certificate issue
            //     ]);

            //     if($response3->getStatusCode() == 200){

            //         $productLimit = json_decode($response3->getBody());
            //     }

            $response2 = $client->request('post', '/wp-json/wc/v3/products/340/variations', [
                'headers' => [
                    "Authorization" => "Basic " . base64_encode($shop->csv_app->app_key . ':' . $shop->csv_app->app_password)
                ],
                'verify' => false, //only needed if you are facing SSL certificate issue
                'json' => $data->variations,
            ]);

            if ($response2->getStatusCode() == 200 || $response2->getStatusCode() == 201) {

                $csv_product = json_decode($response2->getBody());
            }

            // Upload images
            // foreach ($csv_product as $variant) {

            //     $local_variant = $product->variants->where('sku', $csv_product->sku)->first();

            //     if($local_variant->img_source){
            //         $data = [
            //             'image' => (object)[
            //                 'src' => $local_variant->img_source,
            //                 'variant_ids' => [
            //                     $csv_product->id
            //                 ]
            //             ]
            //         ];
            //         $response = $client->request('post', '/wp-json/wc/v3/products/'.$csv_product->id.'/images.json', [
            //             'headers' => [
            //                 "Authorization" => "Basic ". base64_encode($shop->csv_app->app_key.':'.$shop->csv_app->app_password)
            //             ],
            //             'verify' => false, //only needed if you are facing SSL certificate issue
            //             'json' => $data,
            //             ]);
            //         // $response = $client->request('POST', 'https://'.$shop->csv_app->app_key.':'.$shop->shopify_app->app_password.'@'.$shop->shopify_app->domain.'.myshopify.com/admin/api/2020-04/products/'.$shopify_product->id.'/images.json');
            //     }
            // }

            // ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['csv_product_id' => $csv_product->id]);

            return $csv_product;
        } catch (\Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

            return false;
        }
    }

    public static function updateOrderShipping($supplier_order, $csv_order_id, $shipping)
    {
        $shop = $supplier_order->order->shop;

        $client = new \GuzzleHttp\Client([
            // Base URI is used with relative requests
            'base_uri' => $shop->csv_app->domain,
        ]);
        $response = $client->request('GET', '/wp-json/wc/v3/orders/' . $csv_order_id, [
            'headers' => [
                "Authorization" => "Basic " . base64_encode($shop->csv_app->app_key . ':' . $shop->csv_app->app_password)
            ],
            'verify' => false, //only needed if you are facing SSL certificate issue
        ]);

        if ($response->getStatusCode() == 200) {
            $csv_order = json_decode($response->getBody())->order;
        }

        if ($csv_order->fulfillment_status == 'fulfilled') {
            return true;
        }

        $line_items = [];

        try {
            $item = $supplier_order->items->first();
            $item = $supplier_order->order->items->where('product_variant_id', $item->product_variant_id)->first();

            $response = $client->request('GET', '/wp-json/wc/v3/variants/' . $item->external_variant_id, [
                'headers' => [
                    "Authorization" => "Basic " . base64_encode($shop->csv_app->app_key . ':' . $shop->csv_app->app_password)
                ],
                'verify' => false, //only needed if you are facing SSL certificate issue
            ]);

            if ($response->getStatusCode() == 200) {
                $variant = json_decode($response->getBody())->variant;
            }

            $response = $client->request('GET', '/wp-json/wc/v3/products?shipping_class_id=' . $item->external_variant_id, [
                'headers' => [
                    "Authorization" => "Basic " . base64_encode($shop->csv_app->app_key . ':' . $shop->csv_app->app_password)
                ],
                'verify' => false, //only needed if you are facing SSL certificate issue
            ]);

            // $response = $client->request('GET', 'https://'.$shop->shopify_app->app_key.':'.$shop->shopify_app->app_password.'@'.$shop->shopify_app->domain.'.myshopify.com/admin/api/2020-04/inventory_levels.json?inventory_item_ids='.$variant->inventory_item_id);

            if ($response->getStatusCode() == 200) {
                $inventory_levels = json_decode($response->getBody())->inventory_levels;
            }

            $location_id = collect($inventory_levels)->first()->location_id;

            foreach ($csv_order->line_items as $item) {
                $order_item = $supplier_order->order->items->where('external_variant_id', $item->variant_id)->first();

                if ($order_item) {
                    if (in_array($order_item->product_variant_id, $supplier_order->items->pluck('product_variant_id')->toArray())) {
                        $line_items[] = (object)['id' => $item->id];
                    }
                }
            }

            $data = [
                'fulfillment' => (object)[
                    'notify_customer' => true,
                    'location_id' => $location_id,
                    'tracking_company' => $shipping->company,
                    'tracking_numbers' => [
                        $shipping->tracking_number
                    ],
                    'tracking_urls' => [
                        $shipping->tracking_url
                    ],
                    'line_items' => $line_items
                ]
            ];

            $response = $client->request('post', '/wp-json/wc/v3/orders/' . $csv_order_id . '/fulfillments.json', [
                'headers' => [
                    "Authorization" => "Basic " . base64_encode($shop->csv_app->app_key . ':' . $shop->csv_app->app_password)
                ],
                'verify' => false, //only needed if you are facing SSL certificate issue
                'json' => [$data,]
            ]);

            // $response = $client->request('POST', 'https://'.$shop->shopify_app->app_key.':'.$shop->shopify_app->app_password.'@'.$shop->shopify_app->domain.'.myshopify.com/admin/api/2020-04/orders/'.$shopify_order_id.'/fulfillments.json');

            if ($response->getStatusCode() == 201) {
                $fulfillment = json_decode($response->getBody())->fulfillment;

                $supplier_order->shipping->external_service = 'planilha';
                $supplier_order->shipping->external_fulfillment_id = $fulfillment->id;
                $supplier_order->shipping->save();

                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

            return false;
        }
    }



    public static function registerProductJson($shop, $product)
    {
        //registra um produto na csv e retorna o id em caso de sucesso ou error em caso de falha
        try {
            //carrega os dados do shop e do produto passados

            if ($shop->status == 'inactive') {
                return false;
            }

            $data = (object)[
                'product' => (object)[
                    'title' => $product->title,
                    'body_html' => $product->description,
                    'vendor' => $product->supplier->name,
                    'published' => false,
                    'options' => [],
                    'variants' => [],
                    'images' => []
                ]
            ];

            if ($product->img_source) {
                $data->product->images[] = (object)['src' => $product->img_source];
            }

            foreach ($product->images as $image) {
                $data->product->images[] = (object)['src' => $image->src];
            }


            foreach ($product->options as $option) {
                $values = [];

                foreach ($product->variants as $variant) {
                    foreach ($variant->options_values->where('product_option_id', $option->id) as $option_value) {
                        $values[] = $option_value->value;
                    }
                }

                $data->product->options[] = (object)[
                    'name' => $option->name
                ];
            }


            foreach ($product->variants as $variant) {
                $i = 1;
                $variant_data = [];

                foreach ($variant->options_values as $option_value) {
                    $variant_data["option" . $i] = $option_value->value;

                    $i++;
                }

                $variant_data['price'] = $variant->price;
                $variant_data['sku'] =  $variant->sku;
                $variant_data['weight'] = ($variant->weight_in_grams != null) ? $variant->weight_in_grams : 0;
                $variant_data['weight_unit'] = 'g';
                $variant_data['fulfillment_service'] = 'manual';

                $data->product->variants[] = (object)$variant_data;

                if ($variant->img_source) {
                    $data->product->images[] = (object)['src' => $variant->img_source];
                }
            }

            $client = new \GuzzleHttp\Client([
                // Base URI is used with relative requests
                'base_uri' => $shop->csv_app->domain,
            ]);

            $response = $client->request('post', '/wp-json/wc/v3/products', [
                'headers' => [
                    "Authorization" => "Basic " . base64_encode($shop->csv_app->app_key . ':' . $shop->csv_app->app_password)
                ],
                'verify' => false, //only needed if you are facing SSL certificate issue
                'json' => [$data,]
            ]);

            //dd(json_decode($response->getBody())->product);
            if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
                return json_decode($response->getBody())->product;
            }
            return false;
        } catch (\Exception $th) {
            return false;
        }
    }


    public static function Importprodutosupplier($request)
    {
       
      $excel = public_path('importprodexcel/'.$request);
       
      $collection = (new Importproduto)->toCollection($excel);   
        
        $uniqueItems = $collection[0]->unique('SKU');    
           
        $array = [];     
        foreach ($uniqueItems as $unique){
            $unique->put('item', $collection[0]->where('SKU', $unique['SKU']));              
            $array[] = $unique ;             
        }
        return $array;
        
       // dd($array);
         
    }

    public static function storeFilesProd($request){
        //salva a planilha primeiro
        if($request->hasFile('arquivo')){
            $supplier = Auth::guard('supplier')->user();             
            
                $upload = $request->file('arquivo');
                $nameShippingLabels = Str::random(15). '.' . $request->arquivo->extension();

                $upload->move(public_path('importprodexcel'),$nameShippingLabels);
          
                        
        }
        
        return $nameShippingLabels;
    }


    public static function validarext($url)
{
    if($url <> null){
    $validar = get_headers($url);
    $validar = explode(" ",$validar[0]);
    $validar = $validar[1];
    if($validar == "302" || $validar == "200")
        return true;
    else
        return false;
    }else{
        return false;

    }   

}


}
