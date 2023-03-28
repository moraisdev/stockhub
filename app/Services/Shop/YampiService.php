<?php

namespace App\Services\shop;

use App\Models\ErrorLogs;
use App\Models\OrderShippings;
use App\Models\ShopProducts;
use App\Models\Orders;
use App\Models\OrderItems;
use App\Models\OrderItemDiscounts;

use App\Models\Customers;
use App\Models\CustomerAddresses;

use App\Models\ProductVariants;
use App\Models\ProductVariantStock;


use App\Services\CurrencyService;
use App\Models\Suppliers;
use App\Models\FreteMelhorEnvio;
use App\Services\CorreiosService;
use App\Services\TotalExpressService;
use App\Services\MelhorEnvioService;
use App\Services\ChinaShippingService;
use Illuminate\Support\Facades\Log;

class YampiService
{
    public static function getPaidOrders($shop = null)
    {
        try {
            $client = new \GuzzleHttp\Client();
            //status_id[]=4 -> pagamento aprovado
            $response = $client->request(
                'GET',
                'https://api.dooki.com.br/v2/'.$shop->yampi_app->domain.'/orders?include=items,variations,customer,shipping_address&status_id[]=4',
                [ 'headers' => [
                            'User-Token' => $shop->yampi_app->app_key,
                            'User-Secret-Key' => $shop->yampi_app->app_password,
                            'Content-Type' => 'application/json',
                        ]

              ]
            );

            $orders = json_decode($response->getBody());

            return ['status' => 'success', 'message' => 'Pedidos buscados no yampi com sucesso.', 'data' => $orders->data];
        } catch (\Exception $e) {
            if ($e->getCode()) {
                return ['status' => 'error', 'message' => 'Não conseguimos buscar seus pedidos no yampi. Verifique se o seu Token no yampi foi configurado corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
            }
        }
    }

    public static function getBrands($shop = null)
    {
        try {
            $brandName = $shop->name;

            $client = new \GuzzleHttp\Client();
            $response = $client->request(
                'GET',
                'https://api.dooki.com.br/v2/'.$shop->yampi_app->domain.'/catalog/brands?q='.$brandName,
                [ 'headers' => [
                            'User-Token' => $shop->yampi_app->app_key,
                            'User-Secret-Key' => $shop->yampi_app->app_password,
                            'Content-Type' => 'application/json',
                        ]

              ]
            );

            $brands = json_decode($response->getBody());

            return $brands->data;
        } catch (\Exception $e) {
            if ($e->getCode()) {
                return ['status' => 'error', 'message' => 'Não conseguimos buscar suas marcas no yampi. Verifique se o seu Token no yampi foi configurado corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
            }
        }
    }

    public static function createBrands($shop = null)
    {
        try {
            $brandValid = YampiService::getBrands($shop);
            if ($brandValid != null) {
                return $brandValid;
            } else {
                $data = (object)[
                    "active" => true,
                    "featured" => false,
                    "name" => $shop->name,
            ];

                $client = new \GuzzleHttp\Client();
                $response = $client->request(
                    'POST',
                    'https://api.dooki.com.br/v2/'.$shop->yampi_app->domain.'/catalog/brands',
                    [   'json' => $data,
                            'headers' => [
                            'User-Token' => $shop->yampi_app->app_key,
                            'User-Secret-Key' => $shop->yampi_app->app_password,
                            'Content-Type' => 'application/json',
                        ]
              ]
                );

                $brands = json_decode($response->getBody());

                return $brands->data;
            }
        } catch (\Exception $e) {
            if ($e->getCode()) {
                return ['status' => 'error', 'message' => 'Não conseguimos buscar suas marcas no yampi. Verifique se o seu Token no yampi foi configurado corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
            }
        }
    }

    public static function registerOrder($shop, $yampi_order)
    {
        try {
            if ($shop->status == 'inactive') {
                return false;
            }

            if (!isset($yampi_order->customer) || !$yampi_order->customer) {
                return false;
            }
            //pega o sku dos itens
            //  return response()->json($shop);
            $yampi_order->items = self::getSkuLineItems($shop, $yampi_order->items->data);
            if (self::checkOrderItems($yampi_order->items)) {
                return true;
            }

            $order = Orders::firstOrNew(['shop_id' => $shop->id, 'external_id' => $yampi_order->id]);
            if ($order->id != null) {
                return true;
            }

            $order->external_service = 'yampi';
            $order->name = $yampi_order->number;
            $order->email = $yampi_order->customer->data->email;
            $order->external_price = $yampi_order->value_total;
            //$order->external_usd_price = $yampi_order->total_price_usd; (não nulo, mudei manualmente no bd local)
            $order->landing_site = null /*complementar*/;
            $order->status = 'pending';
            $order->external_created_at = date('Y-m-d h:i:s', strtotime($yampi_order->created_at->date));
            if (!$order->save()) {
                return null;
            }
            
            $items = self::registerItems($order, $yampi_order->items);

            $yampi_order->customer->data->total_spent = $yampi_order->value_total; //nao vem esse campo no customer pela api do yampi
            
            $customer = self::registerCustomer($shop, $yampi_order->customer->data, $yampi_order->shipping_address->data);
            $shipping = self::registerShipping($order, $items['items'], $customer, $shop);

            if (!$customer || !$items || !$shipping || $items['total_amount'] == 0) {
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

            return $order;
        } catch (\Exception $e) {
            Log::error($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

        }
    }

    //função utilizada para pegar o sku dos itens na yampi
    private static function getSkuLineItems($shop, $yampi_line_items)
    {
        $newLineItems = $yampi_line_items;
        try {
            foreach ($newLineItems as $key => $yampi_item) {

                //precisa fazer uma nova requisição para pegar o sku dos itens no yampi
                $client = new \GuzzleHttp\Client();

                $response = $client->request(
                    'GET',
                    'https://api.dooki.com.br/v2/'.$shop->yampi_app->domain.'/catalog/products/'.$yampi_item->product_id.'/skus?include=variations',
                    [ 'headers' => [
                        'User-Token' => $shop->yampi_app->app_key,
                        'User-Secret-Key' => $shop->yampi_app->app_password,
                        'Content-Type' => 'application/json',
                    ]

                 ]
                );
                $productsVariants = json_decode($response->getBody());
                $productsVariants = $productsVariants->data;

                //busca o id do product variant
                foreach ($productsVariants as $productVariant) {
                    $variant = ProductVariants::where('sku', $productVariant->sku)->first();
                    if ($variant) {
                        $newLineItems[$key]->sku = $variant; //atribui o sku desse produto como sendo o sku da variação dele escolhida
                        continue; //caso tenha encontrado pula para o próximo item
                    }
                }
            }
            return $newLineItems;
        } catch (\Exception $e) {
            Log::error($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return NULL;
        }
    }

    /* Verify if theres a registered product in the shopify order */
    public static function checkOrderItems($yampi_line_items)
    {
        try {
            foreach ($yampi_line_items as $yampi_item) {
                $variant = ProductVariants::where('sku', $yampi_item->sku_id)->first();
                if ($variant) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            Log::error($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    public static function registerItems($order, $yampi_line_items)
    {
        try {
            $items = array();
            $total_amount = 0;

            foreach ($yampi_line_items as $yampi_item) {
                //antes, verifica se é um kit, caso seja, multiplica pela quantidade que indica na string
                $arrSku = explode("-", $yampi_item->item_sku);
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

                    if($variant){
                        $variant_id = $variant->id;
                        $charge_or_not = 1;

                        $amount = ($variant->price * $yampi_item->quantity * $unidadesSku); //salva x vezes a quantidade de items do kit
                        $amount = $amount - ($amount * ($aplicatedDiscount/100));

                        $total_amount += $amount;
                    }else{
                        continue;
                    }

                    $item = new OrderItems();
                    $item->order_id = $order->id;
                    $item->product_variant_id = $variant_id;
                    $item->external_service = 'yampi';
                    $item->external_product_id = $yampi_item->product_id;
                    $item->sku = $stringSku; //sku sem a tag
                    $item->title = $yampi_item->sku->title;
                    $item->quantity = $yampi_item->quantity * $unidadesSku;
                    $item->amount = $amount;
                    $item->external_price = $yampi_item->price;
                    $item->charge = $charge_or_not;

                    $item->save();

                    if($orderItemDiscount){ //caso haja desconto, salva o item o OrderItem
                        $orderItemDiscount->order_item_id = $item->id;
                        $orderItemDiscount->save();
                    }

                    array_push($items, $item);
                }else{ //caso não seja um kit faz com o sku normal
                    
                    $variant = ProductVariants::where('sku', $yampi_item->item_sku)->first();
                    
                    $aplicatedDiscount = 0; //desconto aplicado
                    $orderItemDiscount = NULL; //salva o desconto usado no item (caso exista)

                    if($variant){
                        $variant_id = $variant->id;
                        $charge_or_not = 1;
                                                
                        $amount = ($variant->price * $yampi_item->quantity);
                        $amount = $amount - ($amount * ($aplicatedDiscount/100));

                        $total_amount += $amount;
                    }else{
                        continue;
                    }
                    
                    $item = new OrderItems();

                    $item->order_id = $order->id;
                    $item->product_variant_id = $variant_id;
                    $item->external_service = 'yampi';
                    $item->external_product_id = $yampi_item->product_id;
                    $item->sku = $yampi_item->item_sku;
                    $item->title = $yampi_item->sku->title;
                    $item->quantity = $yampi_item->quantity;
                    $item->amount = $amount;
                    $item->external_price = $yampi_item->price;
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
        } catch (\Exception $e) {
            Log::error($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    public static function registerCustomer($shop, $yampi_customer, $yampi_address)
    {
        try {
            $customer = Customers::firstOrCreate(['shop_id' => $shop->id, 'external_id' => $yampi_customer->id]);
            $customer->external_service = 'yampi';
            $customer->first_name = $yampi_customer->first_name;
            $customer->last_name = $yampi_customer->last_name;
            $customer->email = $yampi_customer->email;
            //$customer->total_spent = str_replace(",", ".", str_replace(".", "", 100));

            if (!$customer->save()) {
                return null;
            }

            $address = self::registerCustomerAddress($customer, $yampi_customer, $yampi_address);

            if (!$address) {
                return null;
            }
            return $customer;
        } catch (\Exception $e) {
            Log::error($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    public static function registerCustomerAddress($customer, $yampi_customer, $yampi_address)
    {
        try {
            $address = CustomerAddresses::firstOrCreate(['customer_id' => $customer->id, 'address1' => $yampi_address->full_address]);
            $address->name = $yampi_customer->name;
            $address->city = $yampi_address->city;
            $address->province = $yampi_address->state;
            $address->country = $yampi_address->country;
            $address->zipcode = $yampi_address->zipcode;
            $address->province_code = $yampi_address->uf;
            $address->country_code = $yampi_address->country;
            $address->phone = $yampi_customer->phone->full_number;
            $address->company = $yampi_customer->razao_social;
            $address->address1 = $yampi_address->full_address;

            if (!$address->save()) {
                $customer->delete();
                return null;
            }
            return $address;
        } catch (\Exception $e) {
            Log::error($e);
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

                    if ($supplier->shipping_fixed_fee && $supplier->shipping_fixed_fee > 0) {
                        $shipping_amount += $supplier->shipping_fixed_fee;
                    }

                    $shipping = new OrderShippings();

                    $shipping->supplier_id = $supplier->id;
                    $shipping->order_id = $order->id;

                    if ($supplier->id == 56) { //caso seja a s2m2 adiciona os 5% de taxa no frete do produto também
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
            Log::error($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    //por conta da melhor envios agora tbm é necessário os dados do lojista
    public static function calculateShipping($items, $supplier, $customer, $shop = NULL, $order = NULL){
        try{
            //caso seja o pessoal da s2m2, verifica o método de envio dos china
            if($supplier->id == 56){
                $chinaShippingService = new ChinaShippingService();

                $address = $customer->address;
                
                $products = $chinaShippingService->prepareOrderProducts($items);

                $chinaShippingService->setToZipcode($address->zipcode);
                $chinaShippingService->calcBoxWeight($products);

                $valor = $chinaShippingService->getShippingPrice();
                
                if($valor && $valor > 0){
                    return $valor;
                }
            }

            if($supplier->shipping_method == 'correios' && $supplier->correios_settings){
                
                $address = $customer->address;
                $products = CorreiosService::prepareOrderProducts($items);

                $correiosService = new CorreiosService();

                $correiosService->setFromZipcode($supplier->address->zipcode);
                $correiosService->setToZipcode($address->zipcode);
                $correiosService->calcBoxSize($products);

                $result = $correiosService->getShippingPrices($supplier);

                if($result->pac && $result->pac > 0){
                    return $result->pac * ($supplier->correios_settings->percentage / 100);
                }
            }

            if($supplier->shipping_method == 'total_express' && $supplier->total_express_settings){
                
                $address = $customer->address;
                $products = TotalExpressService::prepareOrderProducts($items);

                $totalExpressService = new TotalExpressService($supplier->total_express_settings);

                $totalExpressService->setToZipcode($address->zipcode);
                $totalExpressService->calcBoxSize($products);

                $valor = $totalExpressService->getValorServico();

                if($valor && $valor > 0){
                    return $valor;
                }
            }

            if($supplier->shipping_method == 'melhor_envio' /*&& $supplier->melhor_envio_settings*/){
                $address = $customer->address;

                $melhorEnvioService = new MelhorEnvioService();
                $melhorEnvioService->setFromZipcode($supplier->address->zipcode);
                $melhorEnvioService->setToZipcode($address->zipcode);
                $melhorEnvioService->prepareOrderProducts($items);
                
                //só retorna o valor da cotacao
                $valor = $melhorEnvioService->quoteFreightMinValue();

                if($valor && $valor > 0){
                    return $valor;
                }
            }
        }catch(\Exception $e){
            report($e);
        }

        return 0;
    }

    public static function registerProduct($shop, $product)
    {
        try {
            if ($shop->status == 'inactive') {
                return false;
            }

            //cria um objeto no formato da yampi para enviar na requisição
            $data = (object)[
                'product' => (object)[
                    'title' => $product->title,
                    'body_html' => $product->description,
                    'vendor' => $product->supplier->name,
                    'tags' => '', //tags relacionadas ao produto, obrigatória (pode ser uma string vazia)
                    'options' => [],
                    'variants' => [],
                    'images' => []
                ]
            ];

             if($product->img_source){
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
                    'name' => $option->name,
                    'values' => [$option->name]
                ];
            }

            foreach ($product->variants as $variant) {
                $i = 1;
                $variant_data = [];

                //campos obrigatórios do yampi
                $variant_data["option1"] = null;
                $variant_data["option2"] = null;
                $variant_data["option3"] = null;

                if ($variant->options_values) { //caso o produto tenha opções
                    foreach ($variant->options_values as $option_value) {
                        $variant_data["option".$i] = $option_value->value;
                        $i++;
                    }
                }

                $consultproduto = ProductVariants::where('sku', $variant->sku)->first();
                $stock = ProductVariantStock::where('product_variant_id', $consultproduto->id)->first(); 

                $variant_data["inventory_quantity"] = $stock->quantity;

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

            $client = new \GuzzleHttp\Client();

            $response = $client->request(
                'GET',
                'https://api.dooki.com.br/v2/'.$shop->yampi_app->domain.'/catalog/products/',
                [ 'headers' => [
                    'User-Token' => $shop->yampi_app->app_key,
                    'User-Secret-Key' => $shop->yampi_app->app_password,
                    'Content-Type' => 'application/json',
                ]

            ]
            );

            $yampi_product = json_decode($response->getBody())->product;

            //caso dê certo atualiza o produto com o id vindo do yampi
            ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['yampi_product_id' => $yampi_product->id]);

            return $yampi_product;
        } catch (\Throwable $e) {
            Log::error($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

            return false;
        }
    }

    public static function updateOrderShipping($supplier_order, $yampi_order_id, $shipping) {        
        // 1 - carrega a ordem, verifica se é uma ordem válida ainda (se não foi processada)
        // 2 - caso não tenha sido processado, faz uma requisição para marcar atualizar o rastreio e marcar a ordem como processada
        // 3 - caso tenha sido atualizada com sucesso na yampi, atualiza na mawa também que foi processada e enviada corretamente 
        /*
            status - Em transporte / on_carriage

            status_id - 6
        */

        try {
            $shop = $supplier_order->order->shop;
            $client = new \GuzzleHttp\Client();
            $response = $client->request(
                'GET',
                'https://api.dooki.com.br/v2/'.$shop->yampi_app->domain.'/orders/'.$yampi_order_id,
                [ 'headers' => [
                        'User-Token' => $shop->yampi_app->app_key,
                        'User-Secret-Key' => $shop->yampi_app->app_password,
                        'Content-Type' => 'application/json',
                    ]

                ]
            );

            if ($response->getStatusCode() == 200) {
                $data = [
                    'delivered' => true,
                    'shipment_service' => $shipping->company,
                    'track_url' => $shipping->tracking_url,
                    'track_code' => $shipping->tracking_number,
                    'status_id' => 6,
                    'status' => 'on_carriage',
                    'status_details' => 'Atualizado por '.config('app.name').'. Ordem Fornecedor ID - '.$supplier_order->display_id
                ];
        
                $responsePut = $client->request(
                    'PUT',
                    'https://api.dooki.com.br/v2/'.$shop->yampi_app->domain.'/orders/'.$yampi_order_id,
                    [
                        'json' => $data,
                        'headers' => [
                            'User-Token' => $shop->yampi_app->app_key,
                            'User-Secret-Key' => $shop->yampi_app->app_password,
                            'Content-Type' => 'application/json',
                        ]
        
                    ]
                );

                if ($responsePut->getStatusCode() == 200) {
                    $supplier_order->shipping->external_service = 'yampi';
                    $supplier_order->shipping->save();

                    return true;
                } else {
                    return false;
                }
            }
            
        } catch (\Exception $e) {
            Log::error($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

            return false;
        }
    }

    public static function updateFulfillment($supplier_order, $shipping)
    {
        $shop = $supplier_order->order->shop;

        try {
            $client = new \GuzzleHttp\Client();

            $data = [
                'fulfillment' => (object)[
                    'id' => $shipping->external_fulfillment_id,
                    'notify_customer' => 1,
                    'tracking_company' => $shipping->company,
                    'tracking_numbers' => [
                        $shipping->tracking_number
                    ],
                    'tracking_urls' => [
                        $shipping->tracking_url
                    ],
                ]
            ];

            $response = $client->request(
                'GET',
                'https://api.dooki.com.br/v2/'.$shop->yampi_app->domain.'/catalog/products/',
                [ 'headers' => [
                        'User-Token' => $shop->yampi_app->app_key,
                        'User-Secret-Key' => $shop->yampi_app->app_password,
                        'Content-Type' => 'application/json',
                        ]

            ]
            );

            if ($response->getStatusCode() == 200) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            Log::error($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);

            return false;
        }
    }
    public static function registerProductJson($shop, $product)
    {
        if ($product->public == 1) {
            $active = true;
        } else {
            $active = false;
        }

        //registra um produto na yampi e retorna o id em caso de sucesso ou error em caso de falha
        try {
            //carrega os dados do shop e do produto passados
            
            if ($shop->status == 'inactive') {
                return false;
            }

            $brand = YampiService::createBrands($shop);
            
            $data = (object)[
                    "simple"=> false,
                    "brand_id" => $brand[0]->id,
                    "active" =>$active,
                    "ncm" => preg_replace('/[\.\-\,\" "]+/', '',$product->ncm),
                    "name" => $product->title,
                    "video" => "",
                    "description" => $product->description,
                    "availability" => 5,
                    "quantity_managed" => true,
                    
                    // 'options' => [],
                    'skus' => [],
                    'images' => [] ,
                    // 'variations'=>[],
            ];

            if ($product->img_source) {
                $data->images[] = (object)['url' => $product->img_source];
            }
            
            foreach ($product->images as $image) {
                $data->images[] = (object)['url' => $image->src];
            }

            foreach ($product->options as $option) {
                $values = [];

                foreach ($product->variants as $variant) {
                    foreach ($variant->options_values->where('product_option_id', $option->id) as $option_value) {
                        $values[] = $option_value->value;
                    }
                }
                // $data->variations[] = (object)[
                //     'name' => $option->name
                // ];
            }
                         
            foreach ($product->variants as $variant) {
                $i = 1;
                $variant_data = [];
                $variantions = [];

                foreach ($variant->options_values as $option_value) {
                    $variantions['name'] = $option_value->value;
                    $i++;
                }

                if ($variant->internal_cost != null) {
                    $price = $variant->internal_cost;
                } else {
                    $price = $variant->price;
                }

                $variant_data['title'] = $variant->title;
                $variant_data['price_cost'] = $price;
                $variant_data['price_sale'] = $variant->price;
                $variant_data['blocked_sale'] = false;
                $variant_data['sku'] =  $variant->sku;
                $variant_data['weight'] = ($variant->weight_in_grams != null) ? $variant->weight_in_grams : 0;
                $variant_data['variations'] = $variantions;
                $variant_data['quantity_managed'] = true;
     

          
                

                             
                $client = new \GuzzleHttp\Client();

                if (isset($variantions['name'])) {
                    $dataResponse = $client->request(
                        'GET',
                        'https://api.dooki.com.br/v2/'.$shop->yampi_app->domain.'/catalog/variations?q='.$variantions['name'],
                        [
                                            'headers' => [
                                            'User-Token' => $shop->yampi_app->app_key,
                                            'User-Secret-Key' => $shop->yampi_app->app_password,
                                            'Content-Type' => 'application/json',
                                            ]
                    
                                ]
                    );
                } else {
                    $dataResponse = $client->request(
                        'GET',
                        'https://api.dooki.com.br/v2/'.$shop->yampi_app->domain.'/catalog/variations?q='.$variant->title,
                        [
                            'headers' => [
                                'User-Token' => $shop->yampi_app->app_key,
                                'User-Secret-Key' => $shop->yampi_app->app_password,
                                'Content-Type' => 'application/json',
                            ]
            
                        ]
                    );
                }
                    
                $variationYampi = json_decode($dataResponse->getBody())->data;

                if (count($variationYampi) > 0) {
                    $variationYampiId[] = $variationYampi[0]->id;
                } else {
                    $client = new \GuzzleHttp\Client();
    
                    $dataParameter = (object)[
                            "name"=> $variant->title,
                        ];
   
                    $dataResponse = $client->request(
                        'POST',
                        'https://api.dooki.com.br/v2/'.$shop->yampi_app->domain.'/catalog/variations/',
                        [   'json' => $dataParameter,
                            'headers' => [
                            'User-Token' => $shop->yampi_app->app_key,
                            'User-Secret-Key' => $shop->yampi_app->app_password,
                            'Content-Type' => 'application/json',
                            ]
                        ]
                    );
                    $variationYampi = json_decode($dataResponse->getBody())->data;
                    
                    $variationYampiId[] = $variationYampi->id;
                }
    
                $variant_data['variations_values_ids'] = $variationYampiId;
                
                // $variant_data['weight_unit'] = 'g';
                // $variant_data['fulfillment_service'] = 'manual';

                $data->skus[] = (object)$variant_data;

                if ($variant->img_source) {
                    $data->images[] = (object)['url' => $variant->img_source];
                }
                
            }

            //  return response()->json($data);
            $client = new \GuzzleHttp\Client();
            $response = $client->request(
                'POST',
                'https://api.dooki.com.br/v2/'.$shop->yampi_app->domain.'/catalog/products/',
                [
                    'json' => $data,
                    'headers' => [
                        'User-Token' => $shop->yampi_app->app_key,
                        'User-Secret-Key' => $shop->yampi_app->app_password,
                        'Content-Type' => 'application/json',
                    ]
                ]
            );

            if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
                return json_decode($response->getBody());
            }

            // atualizar estoque
            $variant_estoque = [];
            $variant_estoque['stock_id'] = 1;
            $variant_estoque['quantity'] = 100;
            $variant_estoque['min_quantity'] = 1;

            $data_estoque = (object)$variant_estoque;


            $client = new \GuzzleHttp\Client();
            $response = $client->request(
                'POST',
                'https://api.dooki.com.br/v2/'.$shop->yampi_app->domain.'/catalog/skus/STO1201/stocks',
                [
                    'json' => $data_estoque,
                    'headers' => [
                        'User-Token' => $shop->yampi_app->app_key,
                        'User-Secret-Key' => $shop->yampi_app->app_password,
                        'Content-Type' => 'application/json',
                    ]
                ]
            );

            if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201) {
                return json_decode($response->getBody());
            }







            return false;
        } catch (\Exception $th) {
            Log::error($e);
            return $th->getMessage();
        }
    }

    public static function registerImagesProductJson($shop, $yampi_product, $product)
    {
        try {
            // Upload images
            $yampi_product = (object)$yampi_product;
            
            $client = new \GuzzleHttp\Client();

            foreach ($yampi_product->variants as $variant) {
                $variant = (object)$variant;
                $local_variant = $product->variants->where('sku', $variant->sku)->first();

                if ($local_variant->img_source) {
                    $data = [
                        'image' => (object)[
                            'src' => $local_variant->img_source,
                            'variant_ids' => [
                                $variant->id
                            ]
                        ]
                    ];

                    $response = $client->request('POST', 'https://'.$shop->yampi_app->app_key.':'.$shop->yampi_app->app_password.'@'.$shop->yampi_app->domain.'.myyampi.com/admin/api/2021-04/products/'.$yampi_product->id.'/images.json', ['json' => $data]);
                }
            }
            ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['yampi_product_id' => $yampi_product->id]);

            return true;
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }
}
