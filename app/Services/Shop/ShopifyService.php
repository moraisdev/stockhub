<?php namespace App\Services\Shop;

use App\Models\CustomerAddresses;
use App\Models\Customers;
use App\Models\ErrorLogs;
use App\Models\OrderItems;
use App\Models\Orders;
use App\Models\OrderShippings;
use App\Models\ProductVariants;
use App\Models\ShopifyApps;
use App\Models\ShopProducts;
use App\Models\Suppliers;
use App\Services\ChinaShippingService;
use App\Services\CorreiosService;
use App\Services\MelhorEnvioService;
use App\Services\TotalExpressService;

use App\Models\ProductVariantStock;


use Exception;
use HTTP_Request2;
use Illuminate\Support\Facades\Log;

class ShopifyService
{
    public static function getPaidOrders($shop, $limit = 250)
    {

       // echo '<pre>';
        //15
        $productsService = new ProductsService($shop);
       
        $product = $productsService->find(15);
        //dd($product);
        ShopifyService::registerProductJson($shop,$product);


          /*try {
            $response = ShopifyService::GuzzleCalls($shop, 'GET', 'orders.json?financial_status=paid&limit=' . $limit);
            if ($response->getStatus() == 200) {
                $orders = json_decode($response->getBody())->orders;
            }
            return ['status' => 'success', 'message' => 'Pedidos buscados no shopify com sucesso.', 'data' => $orders];
        } catch (Exception $e) {
            if ($e->getCode() == 401) {
                return ['status' => 'error', 'message' => 'Não conseguimos buscar seus pedidos no shopify. Verifique se o seu APP PRIVADO no shopify foi configurado corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
            } else {
                report($e);
                Log::error($e);
                ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            }
        }*/
    }

    public static function getPaidOrdersLimit($shop)
    {
        try {

            $limit = 100;  
            if ($limit <= 0)
                $limit = 100;

            $response = ShopifyService::GuzzleCalls($shop, 'GET', 'orders.json?financial_status=paid&limit=' . $limit);
            if ($response->getStatus() == 200) {
                $orders = json_decode($response->getBody())->orders;
            }
            return ['status' => 'success', 'message' => 'Pedidos buscados no shopify com sucesso.', 'data' => $orders];
        } catch (Exception $e) {
            if ($e->getCode() == 401) {
                return ['status' => 'error', 'message' => 'Não conseguimos buscar seus pedidos no shopify. Verifique se o seu APP PRIVADO no shopify foi configurado corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
            } else {
                report($e);
                ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            }
        }
    }

    public static function getAllOrders($shop, $orders, $link_header)
    {
        ErrorLogs::create(['status' => 0, 'message' => $link_header, 'file' => '0']);

        $url = parse_url($link_header);

        parse_str($url['query'], $params);

        $pageInfo = substr($params['page_info'], 0, strpos($params['page_info'], ">;"));

        if ($pageInfo) {
            $response = ShopifyService::GuzzleCalls($shop, 'GET', 'orders.json?page_info=' . $pageInfo . '&limit=50');
            if ($response->getStatus() == 200) {
                $orders = array_merge($orders, json_decode($response->getBody())->orders);

                if ($response->getHeaderLine('Link')) {
                    $orders = self::getAllOrders($shop, $orders, $response->getHeaderLine('Link'));
                }
            }
        }

        return $orders;
    }

    public static function registerOrder($shop, $shopify_order)
    {
        try {
            if ($shop->status == 'inactive') {
                return false;
            }

            if (!isset($shopify_order->customer) || !$shopify_order->customer) {
                return false;
            }

            if (!self::checkOrderItems($shopify_order->line_items)) {
                return false;
            }

            $order = new Orders();
            $order->shop_id = $shop->id;
            $order->external_id = $shopify_order->id;
            $order->external_service = 'shopify';
            $order->email = $shopify_order->contact_email;
            $order->external_price = $shopify_order->total_price;
            $order->landing_site = $shopify_order->landing_site;
            $order->status = 'pending';
            $order->external_created_at = date('Y-m-d h:i:s', strtotime($shopify_order->created_at));
            $order->name = $shopify_order->customer->first_name .' ' .$shopify_order->customer->last_name ;
            $order->save();
            if ($order->save()) {
                // Salvo com sucesso
            } else {
                // Ocorreu um erro ao salvar
                return null;
            }

            
            $items = self::registerItems($order, $shopify_order->line_items);
            $customer = self::registerCustomer($shop, $shopify_order->customer, $shopify_order->shipping_address);
            dd($customer);
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
            report($e);
            Log::error($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    /* Verify if theres a registered product in the shopify order */
    public static function checkOrderItems($shopify_line_items)
    {
        try {
            foreach ($shopify_line_items as $shopify_item) {
                //caso seja um kit, retira os dados kit-unidades antes de fazer essa verificação
                $arrSkuCheck = explode("-", $shopify_item->sku);

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
                    $variant = ProductVariants::where('sku', $shopify_item->sku)->first();
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

    public static function registerItems($order, $shopify_line_items)
    {
        try {
            $items = array();
            $total_amount = 0;

            foreach ($shopify_line_items as $shopify_item) {
                //antes, verifica se é um kit, caso seja, multiplica pela quantidade que indica na string
                $arrSku = explode("-", $shopify_item->sku);

                //a string deve ter o padrão kit-unidades-skuitem
                if (count($arrSku) > 2 && $arrSku[1]) { //caso tenha a segunda posição no vetor
                    $unidadesSku = intval($arrSku[1]);
                } else {
                    $unidadesSku = 0;
                }

                if (strtoupper($arrSku[0]) == "KIT" && $unidadesSku > 0) {
                    $stringSku = "";
                    for ($i = 2; $i < count($arrSku); $i++) {
                        $stringSku .= $arrSku[$i] . ($i < count($arrSku) - 1 ? "-" : ""); //monta a string novamente
                    }

                    $variant = ProductVariants::where('sku', $stringSku)->first();

                    $aplicatedDiscount = 0; //desconto aplicado
                    $orderItemDiscount = NULL; //salva o desconto usado no item (caso exista)

                    if ($variant) {
                        $variant_id = $variant->id;
                        $charge_or_not = 1;

                        //antes de fazer a soma desse valor, verifica se o produto fornece desconto por quantidade
                        //caso ofereça, calcula qual é o desconto e salva, depois soma o valor subtraindo o desconto (porcentagem sobre a quantidade)
                        // if(isset($variant->product->discounts) && count($variant->product->discounts) > 0){ //caso possua algum desconto
                        //     $indiceDiscount = 0;
                        //     $minDistance = 9999999; //distancia entre a quantidade e algum desconto oferecido
                        //     $distance = 9999999;
                        //     $orderItemDiscount = new OrderItemDiscounts();

                        //     foreach ($variant->product->discounts as $key => $discount) {
                        //         $distance = ($shopify_item->quantity * $unidadesSku) - $discount->quantity;
                        //         if($distance >= 0 && $distance < $minDistance){
                        //             $indiceDiscount = $key;
                        //             $minDistance = $distance;
                        //             $aplicatedDiscount = $discount->value; //valor do desconto à ser aplicado
                        //             $orderItemDiscount->product_discount_id = $discount->id;
                        //         }
                        //     }
                        //     //caso a distância seja 0, quer dizer q não há descontos
                        //     if($distance < 0 && $minDistance == 9999999){
                        //         $orderItemDiscount = NULL;
                        //     }
                        // }

                        $amount = ($variant->price * $shopify_item->quantity * $unidadesSku); //salva x vezes a quantidade de items do kit
                        $amount = $amount - ($amount * ($aplicatedDiscount / 100));

                        $total_amount += $amount;
                    } else {
                        continue;
                    }

                    $item = new OrderItems();
                    $item->order_id = $order->id;
                    $item->product_variant_id = $variant_id;
                    $item->external_service = 'shopify';
                    $item->external_product_id = $shopify_item->product_id;
                    $item->external_variant_id = $shopify_item->variant_id;
                    $item->sku = $stringSku; //sku sem a tag
                    $item->title = $shopify_item->title;
                    $item->quantity = $shopify_item->quantity * $unidadesSku;
                    $item->amount = $amount;
                    $item->external_price = $shopify_item->price;
                    $item->charge = $charge_or_not;

                    $item->save();

                    if ($orderItemDiscount) { //caso haja desconto, salva o item o OrderItem
                        $orderItemDiscount->order_item_id = $item->id;
                        $orderItemDiscount->save();
                    }

                    array_push($items, $item);
                } else { //caso não seja um kit faz com o sku normal

                    $variant = ProductVariants::where('sku', $shopify_item->sku)->first();

                    $aplicatedDiscount = 0; //desconto aplicado
                    $orderItemDiscount = NULL; //salva o desconto usado no item (caso exista)

                    if ($variant) {
                        $variant_id = $variant->id;
                        $charge_or_not = 1;

                        //antes de fazer a soma desse valor, verifica se o produto fornece desconto por quantidade
                        //caso ofereça, calcula qual é o desconto e salva, depois soma o valor subtraindo o desconto (porcentagem sobre a quantidade)
                        // if(isset($variant->product->discounts) && count($variant->product->discounts) > 0){ //caso possua algum desconto
                        //     $indiceDiscount = 0;
                        //     $minDistance = 9999999; //distancia entre a quantidade e algum desconto oferecido
                        //     $distance = 9999999;
                        //     $orderItemDiscount = new OrderItemDiscounts();

                        //     foreach ($variant->product->discounts as $key => $discount) {
                        //         $distance = $shopify_item->quantity - $discount->quantity;
                        //         if($distance >= 0 && $distance < $minDistance){
                        //             $indiceDiscount = $key;
                        //             $minDistance = $distance;
                        //             $aplicatedDiscount = $discount->value; //valor do desconto à ser aplicado
                        //             $orderItemDiscount->product_discount_id = $discount->id;
                        //         }
                        //     }
                        //     //caso a distância seja 0, quer dizer q não há descontos
                        //     if($distance < 0 && $minDistance == 9999999){
                        //         $orderItemDiscount = NULL;
                        //     }
                        // }

                        $amount = ($variant->price * $shopify_item->quantity);
                        $amount = $amount - ($amount * ($aplicatedDiscount / 100));

                        $total_amount += $amount;
                    } else {
                        continue;
                    }

                    $item = new OrderItems();

                    $item->order_id = $order->id;
                    $item->product_variant_id = $variant_id;
                    $item->external_service = 'shopify';
                    $item->external_product_id = $shopify_item->product_id;
                    $item->external_variant_id = $shopify_item->variant_id;
                    $item->sku = $shopify_item->sku;
                    $item->title = $shopify_item->title;
                    $item->quantity = $shopify_item->quantity;
                    $item->amount = $amount;
                    $item->external_price = $shopify_item->price;
                    $item->charge = $charge_or_not;

                    $item->save();

                    if ($orderItemDiscount) { //caso haja desconto, salva o item o OrderItem
                        $orderItemDiscount->order_item_id = $item->id;
                        $orderItemDiscount->save();
                    }

                    array_push($items, $item);
                }
            }
            return ['items' => $items, 'total_amount' => $total_amount];
        } catch (\Exception $e) {
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

                    if ($supplier->shipping_fixed_fee && $supplier->shipping_fixed_fee > 0) {
                        $shipping_amount += $supplier->shipping_fixed_fee;
                    }

                    $shipping = new OrderShippings();

                    $shipping->supplier_id = $supplier->id;
                    $shipping->order_id = $order->id;

                    if ($supplier->id == 56) { //caso seja a s2m2 adiciona os 5% de taxa no frete do produto também
                        $shipping_amount = $shipping_amount * 1.05;
                    }

                    if ($supplier->id == 43) { //caso seja a ksimports adiciona os R$ 5,00 de taxa no frete
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
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    //por conta da melhor envios agora tbm é necessário os dados do lojista
    public static function calculateShipping($items, $supplier, $customer, $shop = NULL, $order = NULL)
    {
        try {
            //caso seja o pessoal da s2m2, verifica o método de envio dos china
            if ($supplier->id == 56) {
                $chinaShippingService = new ChinaShippingService();

                $address = $customer->address;

                $products = $chinaShippingService->prepareOrderProducts($items);

                $chinaShippingService->setToZipcode($address->zipcode);
                $chinaShippingService->calcBoxWeight($products);

                $valor = $chinaShippingService->getShippingPrice();

                if ($valor && $valor > 0) {
                    return $valor;
                }
            }

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

            if ($supplier->shipping_method == 'melhor_envio' /*&& $supplier->melhor_envio_settings*/) {
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
            }
        } catch (\Exception $e) {
            report($e);
        }

        return 0;
    }

    public static function registerCustomer($shop, $shopify_customer, $shopify_address)
    {
        try {
            $customer = new Customers();
            $customer->shop_id = $shop->id; 
            $customer->external_id = $shopify_customer->id;
            $customer->external_service = 'shopify';
            $customer->first_name = $shopify_customer->first_name;
            $customer->last_name = $shopify_customer->last_name;
            $customer->email = $shopify_customer->email;
            if (!$customer->save()) {
                return null;
            }
            $address = self::registerCustomerAddress($customer, $shopify_address);
            if (!$address) {
                return null;
            }
            return $customer;
        } catch (Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
        }
    }

    public static function registerCustomerAddress($customer, $shopify_address)
    {
        try {
            $address = CustomerAddresses::firstOrCreate(['customer_id' => $customer->id, 'address1' => $shopify_address->address1]);
            $address->name = $shopify_address->name;
            $address->company = $shopify_address->company;
            $address->address2 = $shopify_address->address2;
            $address->city = $shopify_address->city;
            $address->province = $shopify_address->province;
            $address->country = $shopify_address->country;
            $address->zipcode = $shopify_address->zip;
            $address->phone = $shopify_address->phone;
            $address->province_code = $shopify_address->province_code;
            $address->country_code = $shopify_address->country_code;
            if (!$address->save()) {
                $customer->delete();
                return null;
            }
            return $address;
        } catch (Exception $e) {
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
            $data = (object)['product' => (object)['title' => $product->title, 'body_html' => $product->description, 'vendor' => $product->supplier->name, 'published' => false, 'options' => [], 'variants' => [], 'images' => []]];
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
                $data->product->options[] = (object)['name' => $option->name];
            }
            foreach ($product->variants as $variant) {
                $i = 1;
                $variant_data = [];
                foreach ($variant->options_values as $option_value) {
                    $variant_data["option" . $i] = $option_value->value;
                    $i++;
                }
                $variant_data['price'] = $variant->price;
                $variant_data['sku'] = $variant->sku;
                $variant_data['weight'] = ($variant->weight_in_grams != null) ? $variant->weight_in_grams : 0;
                $variant_data['weight_unit'] = 'g';
                $variant_data['fulfillment_service'] = 'manual';
                $data->product->variants[] = (object)$variant_data;
                if ($variant->img_source) {
                    $data->product->images[] = (object)['src' => $variant->img_source];
                }
            }
            $response = ShopifyService::GuzzleCalls($shop, 'POST', 'products.json', false, false, $data);
            if ($response->getStatus() == 200 || $response->getStatus() == 201) {
                $shopify_product = json_decode($response->getBody())->product;
            }
            foreach ($shopify_product->variants as $variant) {
                $local_variant = $product->variants->where('sku', $variant->sku)->first();
                if ($local_variant->img_source) {
                    $data = ['image' => (object)['src' => $local_variant->img_source, 'variant_ids' => [$variant->id]]];
                    $response = ShopifyService::GuzzleCalls($shop, 'POST', 'products/' . $shopify_product->id . '/images.json', false, false, $data);
                }
            }
            ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['shopify_product_id' => $shopify_product->id]);
            return $shopify_product;
        } catch (Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return false;
        }
    }

    public static function updateOrderShipping($supplier_order, $shopify_order_id, $shipping)
    {
        $shop = $supplier_order->order->shop;
        $response = ShopifyService::GuzzleCalls($shop, 'GET', 'orders/' . $shopify_order_id . '.json');

        if ($response->getStatus() == 200) {
            $shopify_order = json_decode($response->getBody())->order;
        }
        if ($shopify_order->fulfillment_status == 'fulfilled') {
            return true;
        }
        $line_items = [];
        try {
            $item = $supplier_order->items->first();
            $item = $supplier_order->order->items->where('product_variant_id', $item->product_variant_id)->first();
            $response = ShopifyService::GuzzleCalls($shop, 'GET', 'variants/' . $item->external_variant_id . '.json');
            if ($response->getStatus() == 200) {
                $variant = json_decode($response->getBody())->variant;
            }
            $response = ShopifyService::GuzzleCalls($shop, 'GET', 'inventory_levels.json?inventory_item_ids=' . $variant->inventory_item_id);
            if ($response->getStatus() == 200) {
                $inventory_levels = json_decode($response->getBody())->inventory_levels;
            }
            $location_id = collect($inventory_levels)->first()->location_id;
            foreach ($shopify_order->line_items as $item) {
                $order_item = $supplier_order->order->items->where('external_variant_id', $item->variant_id)->first();
                if ($order_item) {
                    if (in_array($order_item->product_variant_id, $supplier_order->items->pluck('product_variant_id')->toArray())) {
                        $line_items[] = (object)['id' => $item->id];
                    }
                }
            }
            $data = ['fulfillment' => (object)['notify_customer' => true, 'location_id' => $location_id, 'tracking_company' => $shipping->company, 'tracking_numbers' => [$shipping->tracking_number], 'tracking_urls' => [$shipping->tracking_url], 'line_items' => $line_items]];
            $response = ShopifyService::GuzzleCalls($shop, 'POST', 'orders/' . $shopify_order_id . '/fulfillments.json', false, false, $data);
            if ($response->getStatus() == 201) {
                $fulfillment = json_decode($response->getBody())->fulfillment;
                $supplier_order->shipping->external_service = 'shopify';
                $supplier_order->shipping->external_fulfillment_id = $fulfillment->id;
                $supplier_order->shipping->save();
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return false;
        }
    }

    public static function updateFulfillment($supplier_order, $shipping)
    {
        $shop = $supplier_order->order->shop;
        try {
            $data = ['fulfillment' => (object)['notify_customer' => true, 'tracking_info' => (object)['number' => $shipping->tracking_number, 'url' => $shipping->tracking_url, 'company' => $shipping->company,]]];
            $response = ShopifyService::GuzzleCalls($shop, 'POST', 'fulfillments/' . $shipping->external_fulfillment_id . '/update_tracking.json', false, false, $data);
            if ($response->getStatus() == 200) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return false;
        }
    }

    public static function cancelFulfillment($supplier_order, $shipping)
    {
        $shop = $supplier_order->order->shop;
        try {
            $response = ShopifyService::GuzzleCalls($shop, 'POST', 'fulfillments/' . $shipping->external_fulfillment_id . '/cancel.json');
            if ($response->getStatus() == 200) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            report($e);
            ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            return false;
        }
    }

    public static function registerProductJson($shop, $product)
    {
        //registra um produto na shopify e retorna o id em caso de sucesso ou error em caso de falha
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
                    'images' => [

                    ]
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

                $consultproduto = ProductVariants::where('sku', $variant->sku)->first();
                $stock = ProductVariantStock::where('product_variant_id', $consultproduto->id)->first();

                $variant_data['price'] = '0.00';
                $variant_data['sku'] = $variant->sku;
                $variant_data['option1'] = $variant->sku;
                $variant_data['weight'] = ($variant->weight_in_grams != null) ? $variant->weight_in_grams : 0;
                $variant_data['weight_unit'] = 'g';
                $variant_data['fulfillment_service'] = 'manual';
                $variant_data['inventory_quantity']=  $stock->quantity;
                

                $data->product->variants[] = (object)$variant_data;

                if ($variant->img_source) {
                    $data->product->images[] = (object)['src' => $variant->img_source];
                }
            }

            $response = ShopifyService::GuzzleCalls($shop, 'POST', 'products.json', false, false, $data);
            //dd(json_decode($response->getBody()));
           // $postproduto = json_decode($response->getBody());
           // dd($response);
            
            if($response->getStatus() == 200 || $response->getStatus() == 201){
                    return json_decode($response->getBody())->product;
                }
                return false;
        } catch (\Exception $th) {
           // var_dump(1);
           // var_dump($th);
            die();
            return false;
            ErrorLogs::create(['status' => $th->getCode(), 'message' => $th->getMessage(), 'file' => $th->getFile()]);
        }
    }

    public static function registerImagesProductJson($shop, $shopify_product, $product)
    {
        try {
            $shopify_product = (object)$shopify_product;
            foreach ($shopify_product->variants as $variant) {
                $variant = (object)$variant;
                $local_variant = $product->variants->where('sku', $variant->sku)->first();
                if ($local_variant->img_source) {
                    $data = ['image' => (object)['src' => $local_variant->img_source, 'variant_ids' => [$variant->id]]];
                    $response = ShopifyService::GuzzleCalls($shop, 'POST', 'products/' . $shopify_product->id . '/images.json', false, false, $data);
                }
            }
            ShopProducts::where('shop_id', $shop->id)->where('product_id', $product->id)->update(['shopify_product_id' => $shopify_product->id]);
            return true;
        } catch (Exception $e) {
            report($e);
            return false;
        }
    }

    public static function GuzzleCalls($shop, $method, $event, $responseWithBody = false, $responseWithContentes = false, $data = null)
    {
        try {
            $shopify_app = ShopifyApps::firstOrCreate(['shop_id' => $shop->id]);
            $arrayCommand = array();
            $arrayCommand['uri'] = "https://" . $shopify_app->domain . ".myshopify.com/admin/api/" . $shopify_app->api_version . "/" . $event;
            $arrayCommand['apiKey'] = $shopify_app->app_key;
            $arrayCommand['apiSecretKey'] = $shopify_app->app_password;
            $arrayCommand['X-Shopify-Access-Token'] = $shopify_app->token;
            $arrayCommand['Authorization'] = 'Basic ' . base64_encode($arrayCommand['apiKey'] . ":" . $arrayCommand['apiSecretKey']);
            $arrayCommand['Content-Type'] = "application/json";
            //$headers = ['Content-Type' => $arrayCommand['Content-Type'], 'X-Shopify-Access-Token' => $arrayCommand['X-Shopify-Access-Token'], 'Authorization' => $arrayCommand['Authorization']];

            switch (strtoupper($method)) {
                case 'PUT':
                    $arrayCommand['httpType'] = HTTP_Request2::METHOD_PUT;
                    $arrayCommand['body'] = json_encode($data);
                    break;
                case 'POST':
                    $arrayCommand['httpType'] = HTTP_Request2::METHOD_POST;
                    $arrayCommand['body'] = json_encode($data);
                    break;
                default:
                    $arrayCommand['httpType'] = HTTP_Request2::METHOD_GET;
                    break;
            }

            if ($data == null)
                $arrayCommand['body'] = '';

            $request = new  HTTP_Request2();

            $request->setUrl($arrayCommand['uri']);
            $request->setMethod($arrayCommand['httpType']);
            $request->setConfig(array(
                'follow_redirects' => TRUE
            ));
            $request->setHeader(array(
                'Content-Type' => $arrayCommand['Content-Type'],
                'X-Shopify-Access-Token' => $arrayCommand['X-Shopify-Access-Token'],
                'Authorization' => $arrayCommand['Authorization']
            ));

            $request->setBody($arrayCommand['body']);

            try {
                $response = $request->send();
                if ($response->getStatus() == 200 || $response->getStatus() == 201) {
                    return $response;
                } else {
                    return 'Unexpected HTTP status: ' . $response->getStatus() . ' ' . $response->getReasonPhrase();
                }
            } catch (HTTP_Request2_Exception $e) {
                var_dump(2);
                var_dump($e);
                die();
                if ($e->getCode() == 401) {
                    return ['status' => 'error', 'message' => 'Não conseguimos buscar seus pedidos no shopify. Verifique se o seu APP PRIVADO no shopify foi configurado corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
                } else {
                    report($e);
                    Log::error($e);
                    ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
                }
            }
        } catch (\Exception $e) {
            var_dump(3);
            var_dump($e);
            die();
            if ($e->getCode() == 401) {
                return ['status' => 'error', 'message' => 'Não conseguimos buscar seus pedidos no shopify. Verifique se o seu APP PRIVADO no shopify foi configurado corretamente. Em caso de dúvidas entre em contato com nosso suporte.'];
            } else {
                report($e);
                Log::error($e);
                ErrorLogs::create(['status' => $e->getCode(), 'message' => $e->getMessage(), 'file' => $e->getFile()]);
            }
        }


    }

}
