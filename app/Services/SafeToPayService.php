<?php

namespace App\Services;
use App\Models\Suppliers;
use App\Models\ProductVariants;
use App\Models\Products;
use Safe2Pay\API\PaymentRequest;
use Safe2Pay\API\RefundType;
use Safe2Pay\Models\Payment\BankSlip;
use Safe2Pay\Models\Payment\Cryptocoin;
use Safe2Pay\Models\Payment\CreditCard;
use Safe2Pay\Models\Payment\DebitCard;
use Safe2Pay\Models\Payment\Carnet;
use Safe2Pay\Models\Payment\CarnetLot;
use Safe2Pay\Models\Transactions\Splits;
use Safe2Pay\Models\Transactions\Transaction;
use Safe2Pay\Models\General\Customer;
use Safe2Pay\Models\General\Product;
use Safe2Pay\Models\General\Address;
use App\Models\ErrorLogs;
use App\Models\CouponOrderReturned;
use App\Models\OrderReturned;
use App\Models\DiscountAppliedOrderRefunded;

use Safe2Pay\Models\Core\Config as Enviroment;
use Safe2Pay\Models\Payment\CarnetBankslip;

use Illuminate\Support\Facades\Log;

$enviroment = new Enviroment();
$enviroment->setAPIKEY('0E7BC01AFBE54003AF330EA32D2ED33C'); //oficial
//$enviroment->setAPIKEY('2961AABFEC7048D1BF34AA22FD5EEDF5');

class SafeToPayService{
    private $apikey_sandbox = '2961AABFEC7048D1BF34AA22FD5EEDF5';
    private $apikey = '0E7BC01AFBE54003AF330EA32D2ED33C'; //oficial
    //private $apikey = '2961AABFEC7048D1BF34AA22FD5EEDF5';
    protected $isSandbox = false; //oficial
    //protected $isSandbox = true;

    public static function GetPaymentMethods(){
        //Código da forma de pagamento
        // 1 - Boleto bancário
        // 2 - Cartão de crédito
        // 3 - Criptomoeda
        // 4 - Cartão de débito
        // 6 - Pix
        $response = PaymentRequest::GetPaymentMethods();
        echo(json_encode($response));
    }

    public function pay($group, $payment_method){
        //antes de tudo verifica se o lojista preencheu todos os dados de endereço
        if(!$group->shop->address || !$group->shop->address->zipcode || !$group->shop->address->street || !$group->shop->address->number || !$group->shop->address->district || !$group->shop->address->state_code || !$group->shop->address->city ||
        !$group->shop->address->country){
            return ['status' => 'error', 'message' => 'Seus dados são inválidos, é necessário completar seu perfil com dados de endereço antes de pagar as faturas.'];
        }

        // //caso o tipo de pagamento seja o pix, verifica se pode gerar outro no periodo de tempo determinado
        // if($payment_method == 'pix' && $group->updated_at != $group->created_at){
        //     $hoursWait = 1; //qtd de horas a serem esperadas para gerar outro pix

        //     $currentData = date("Y-m-d H:i:s");
        //     $date1 = date_create($group->updated_at);
        //     $date2 = date_create($currentData);
        //     $diff = date_diff($date1,$date2);

        //     if($diff->h < $hoursWait){ //caso ainda não tenha passado uma hora, não deixa gerar outro pix
        //         return ['status' => 'warning', 'message' => 'É preciso esperar '.$hoursWait.'h para gerar outro Pix.'];
        //     }            
        // }

        $enviroment = new Enviroment();
        $enviroment->setAPIKEY('0E7BC01AFBE54003AF330EA32D2ED33C'); //oficial
        //$enviroment->setAPIKEY('2961AABFEC7048D1BF34AA22FD5EEDF5');

        $products = array();
        $splits = array();
        $caminho = getenv('APP_URL').'api/safe2pay/webhooks/transaction';
        $payload = new Transaction();
        $payload->setIsSandbox($this->isSandbox); //mudar para false quando enviar para produção
        $payload->setApplication(config('app.name'));
        $payload->setVendor(config('app.name'));
        $payload->setCallbackUrl($caminho);

        if($payment_method == 'pix'){
            $payload->setPaymentMethod("6"); //1 - boleto, 6 - pix
        }else{
            $payload->setPaymentMethod("1");
        }

        $bankslip = new BankSlip();

        //calcula o próximo dia útil

        //$bankslip->setDueDate(date('d/m/Y', strtotime('+1 days'))); //old
        $bankslip->setDueDate(self::proximoDiaUtil(date('Y-m-d', strtotime('+1 days'))));

        $bankslip->setInstruction("Pagar até a data de vencimento");
        $bankslip->setPenaltyRate(0.00);
        $bankslip->setInterestRate(0.00);
        $bankslip->setCancelAfterDue(false);
        $bankslip->setIsEnablePartialPayment(false);
        $bankslip->setMessage(array(
            "Sr. Caixa, favor não receber após o vencimento."
        ));

        //Objeto de pagamento para boleto bancário
        $payload->setPaymentObject($bankslip);

        $supplier_amounts = null;

        //caso seja boleto, insere a taxa
        if($payment_method == 'boleto'){
            $tax_product = new Product();
            $tax_product->setCode(9999);
            $tax_product->setDescription("Taxa de processamento ".config('app.name')." (Boleto).");
            $tax_product->setUnitPrice(1.80);
            $tax_product->setQuantity(1);

            array_push($products, $tax_product);
        }

        $countPixValue = 0.0;
        $countRepasseMawa = 0.0;

        foreach($group->orders as $supplier_order){    
            $originalValueSupplierAmount = $supplier_order->amount;
            //verifica se tem algum cupom pra aquele fornecedor em específico
            $orderReturneds = OrderReturned::where('decision', 'credit')
                                        ->where('status', 'solved')
                                        ->where('supplier_id', $supplier_order->supplier->id)
                                        ->where('shop_id', $group->shop->id)
                                        ->pluck('id');

            $valueCouponsReturned = CouponOrderReturned::whereIn('order_returned_id', $orderReturneds)
                                                        ->where('status', 'pending')
                                                        ->sum('amount'); //faz uma query pra somar o valor em cupons ainda a ser descontado
            //dd($supplier_order->amount);
            //$supplier_order->amount = 150.00;
            if($valueCouponsReturned > 0){ //caso possua cupons
                if($supplier_order->amount < $valueCouponsReturned){
                    $amountReturned = 0;
                    $rest = -$supplier_order->amount; //resto
                }else{ //caso seja >=
                    $amountReturned = $supplier_order->amount - $valueCouponsReturned;
                    $rest = -$valueCouponsReturned; 
                }

                CouponOrderReturned::create([
                    'order_returned_id' => $orderReturneds[0], //salva o primeiro cupom só pra identificar que foi usado o valor todo
                    'amount' => $rest, //desconta o valor da ordem
                    'supplier_id' => $supplier_order->supplier->id
                ]);

                //salva que o desconto foi aplicado pra exibir na tela da fatura
                DiscountAppliedOrderRefunded::create([
                    'order_returned_id' => $orderReturneds[0],
                    'supplier_order_group_id' => $group->id,
                    'amount' => $rest
                ]);

                $supplier_order->amount = $amountReturned; //o novo valor dessa ordem vai ser a diferença, ou seja, aplica o desconto nessa ordem
                //dd('valor: '.$amountReturned.' resto '.$rest);
            }

            //adiciona o valor do frete pra mawa como sendo um produto
            //só faz esse repasse pra gente se o tipo de envio do fornecedor for melhor envio
            if($supplier_order->supplier->shipping_method == 'melhor_envio'){
                if($supplier_order->amount){
                    $payloadProduct = new Product();
                    $payloadProduct->setCode($supplier_order->id);
                    $payloadProduct->setDescription("Pedido ".$supplier_order->f_display_id." ".config('app.name').".");
                    $payloadProduct->setUnitPrice($supplier_order->amount);
                    $payloadProduct->setQuantity(1);
                    $countPixValue += $supplier_order->amount;
                }
                
                $payloadFreteMelhorEnvio = new Product();
                $payloadFreteMelhorEnvio->setCode($supplier_order->f_display_id.'-frete');
                $payloadFreteMelhorEnvio->setDescription("Frete Pedido ".$supplier_order->f_display_id." ".config('app.name').".");
                //$payloadFreteMelhorEnvio->setUnitPrice($supplier_order->total_amount - $supplier_order->amount);
                $payloadFreteMelhorEnvio->setUnitPrice($supplier_order->total_amount - $originalValueSupplierAmount);
                $payloadFreteMelhorEnvio->setQuantity(1);   
                //$countPixValue += ($supplier_order->total_amount - $supplier_order->amount); 
                $countPixValue += ($supplier_order->total_amount - $originalValueSupplierAmount);
            }else{
                //caso contrário, repassa o valor todo pro fornecedor
                $payloadProduct = new Product();
                $payloadProduct->setCode($supplier_order->id);
                $payloadProduct->setUnitPrice($supplier_order->total_amount);
                $payloadProduct->setQuantity(1);
                $countPixValue += $supplier_order->total_amount;

                if($supplier_order->supplier->id == 43){ //caso seja a ksimports repassa com R$ 2,00 a menos, que é o que fica pra mawa
                    $payloadProduct->setDescription("Pedido ".$supplier_order->f_display_id." ".config('app.name').". (- R$ 2.00 frete mawa)");
                    $countRepasseMawa += 2.00;
                }else{
                    $payloadProduct->setDescription("Pedido ".$supplier_order->f_display_id." ".config('app.name').".");
                }
            }
            
            foreach($supplier_order->items as $item){
                //caso o item não tenha sido excluído
                if($item->variant){
                    $discount_product = isset($item->variant->product) ? $item->variant->product : new Product();
                    if($discount_product && $discount_product->ignore_percentage_on_tax != null && $discount_product->ignore_percentage_on_tax > 0){
                        if(isset($supplier_amounts[$supplier_order->supplier_id])){
                            $supplier_amounts[$supplier_order->supplier_id]['ignore_percentage_on_tax'] += ($item->variant->price * ($discount_product->ignore_percentage_on_tax/100));
                        }else{
                            $supplier_amounts[$supplier_order->supplier_id]['ignore_percentage_on_tax'] = ($item->variant->price * ($discount_product->ignore_percentage_on_tax/100));
                        }
                    }
                }else{ //caso o item tenha sido excluído
                    $variant = ProductVariants::withTrashed()->find($item->product_variant_id);

                    if($variant){
                        $product = Products::withTrashed()->find($variant->product_id);

                        if($product){
                            $discount_product = isset($product) ? $product : new Product();
                            if($discount_product && $discount_product->ignore_percentage_on_tax != null && $discount_product->ignore_percentage_on_tax > 0){
                                if(isset($supplier_amounts[$supplier_order->supplier_id])){
                                    $supplier_amounts[$supplier_order->supplier_id]['ignore_percentage_on_tax'] += ($variant->price * ($discount_product->ignore_percentage_on_tax/100));
                                }else{
                                    $supplier_amounts[$supplier_order->supplier_id]['ignore_percentage_on_tax'] = ($variant->price * ($discount_product->ignore_percentage_on_tax/100));
                                }
                            }
                        }
                    }
                }
            }

            if(isset($payloadProduct)){
                array_push($products, $payloadProduct);
            }
            
            
            if($supplier_order->supplier->shipping_method == 'melhor_envio'){
                array_push($products, $payloadFreteMelhorEnvio);
            }

            if(isset($supplier_amounts[$supplier_order->supplier_id]) && isset($supplier_amounts[$supplier_order->supplier_id]['total'])){
                //alteracao frete vai para a mawa agora, para o fornecedor agora vai só o valor dos produtos, quem compra as notas é a mawa
                //$supplier_amounts[$supplier_order->supplier_id]['total'] += $supplier_order->total_amount; 
                
                if($supplier_order->supplier->shipping_method == 'melhor_envio'){
                    $supplier_amounts[$supplier_order->supplier_id]['total'] += $supplier_order->amount; 
                }else{
                    $supplier_amounts[$supplier_order->supplier_id]['total'] += $supplier_order->total_amount;                    
                }
                
                $supplier_amounts[$supplier_order->supplier_id]['items'] += $supplier_order->amount;
            }else{
                //alteracao frete vai para a mawa agora, para o fornecedor agora vai só o valor dos produtos, quem compra as notas é a mawa
                //$supplier_amounts[$supplier_order->supplier_id]['total'] = $supplier_order->total_amount;
                if($supplier_order->supplier->shipping_method == 'melhor_envio'){
                    $supplier_amounts[$supplier_order->supplier_id]['total'] = $supplier_order->amount;
                }else{
                    $supplier_amounts[$supplier_order->supplier_id]['total'] = $supplier_order->total_amount;                    
                }
                $supplier_amounts[$supplier_order->supplier_id]['items'] = $supplier_order->amount;
            }
        }

        //caso seja pix, insere a taxa
        if($payment_method == 'pix'){
            $tax_product = new Product();
            $tax_product->setCode(9999);
            $tax_product->setDescription("Taxa de processamento ".config('app.name')." (Pix).");
            $tax_product->setUnitPrice(round($countPixValue * 0.01, 2) + 0.01); //coloca o 1% de taxa do pix (soma 1 centavo pra arredondar pra cima)
            $tax_product->setQuantity(1);

            array_push($products, $tax_product);
        }

        $payload->setProducts($products);

        //dd($supplier_amounts);
        foreach($supplier_amounts as $supplier_id => $amounts){
            $supplier = Suppliers::find($supplier_id);

            $split = new Splits();

            //só adiciona o split se for maior que zero
            if($amounts['total'] - $countRepasseMawa - ($amounts['items'] * ($supplier->mawa_post_tax / 100)) + (isset($amounts['ignore_percentage_on_tax']) && $amounts['ignore_percentage_on_tax'] > 0)){
                $split->setIdReceiver($supplier->safe2pay_subaccount_id);
                $split->setIdentity($supplier->document);
                $split->setName($supplier->name);
                $split->setCodeReceiverType('2');
                $split->setCodeTaxType('2');
                $split->setAmount($amounts['total'] - $countRepasseMawa - ($amounts['items'] * ($supplier->mawa_post_tax / 100)) + (isset($amounts['ignore_percentage_on_tax']) && $amounts['ignore_percentage_on_tax'] > 0 ? $amounts['ignore_percentage_on_tax'] : 0));
                $split->setIsPayTax(false);

                array_push($splits, $split);
            }
        }

        //adiciona o split com o valor do frete pra mawa

        $payload->setSplits($splits);

        $customer = new Customer();
        $customer->setName($group->shop->name);
        $customer->setIdentity($group->shop->document);
        $customer->setEmail($group->shop->email);
        $customer->setPhone($group->shop->phone);

        $customer->Address = new Address();
        $customer->Address->setZipCode($group->shop->address->zipcode);
        $customer->Address->setStreet($group->shop->address->street);
        $customer->Address->setNumber($group->shop->address->number);
        $customer->Address->setComplement($group->shop->address->complement);
        $customer->Address->setDistrict($group->shop->address->district);
        $customer->Address->setStateInitials($group->shop->address->state_code);
        $customer->Address->setCityName($group->shop->address->city);
        $customer->Address->setCountryName($group->shop->address->country);

        $payload->setCustomer($customer);

        //dd($payload);

        $response = PaymentRequest::CreatePayment($payload);

        if($response){
            if($response->HasError == null && $response->ResponseDetail){
                //dd($response);

                if($payment_method == 'pix'){
                    //tem que criar todos os campos do pix separados
                    $group->payment_json_pix = json_encode($response->ResponseDetail);
                    $group->transaction_id_pix = $response->ResponseDetail['IdTransaction'];
                    
                    // //campos pix                    
                    $group->status_pix = $response->ResponseDetail['Status'];
                    $group->message_pix = $response->ResponseDetail['Message'];
                    $group->description_pix = $response->ResponseDetail['Description'];
                    $group->qrcode_pix = $response->ResponseDetail['QrCode'];
                    $group->key_pix = $response->ResponseDetail['Key'];

                    $group->save();

                    return ['status' => 'success', 'message' => 'Pix gerado com sucesso.'];
                }else{
                    $group->payment_json = json_encode($response->ResponseDetail);
                    $group->transaction_id = $response->ResponseDetail['IdTransaction'];
                    $group->bankslip_url = $response->ResponseDetail['BankSlipUrl'];
                    $group->bankslip_digitable_line = $response->ResponseDetail['DigitableLine'];
                    $group->bankslip_barcode = $response->ResponseDetail['Barcode'];
                    $group->bankslip_duedate = date('Y-m-d' ,strtotime($response->ResponseDetail['DueDate']));

                    $group->save();
                    
                    return ['status' => 'success', 'message' => 'Boleto gerado com sucesso.'];
                }

            }else{
                if($response->HasError == true && $response->Error){
                    return ['status' => 'error', 'message' => $response->Error];
                }else{
                    return ['status' => 'error', 'message' => 'Erro ao tentar gerar o boleto.'];
                }
            }
        }
    }

    public function registerSupplierSubAccount($supplier){
        // O exemplo do objeto completo está detalhado abaixo na sessão "Conteúdo de Envio".
        $payload = [
                "Name" => $supplier->legal_name,
                "CommercialName" => $supplier->commercial_name,
                "Identity" => $supplier->document,
                "ResponsibleName" => $supplier->responsible_name,
                "ResponsibleIdentity" => $supplier->responsible_document,
                "Email" => $supplier->email,
                "BankData" => [
                    "Bank" => [
                        "Id" => 0,
                        "Code" => $supplier->bank->code
                    ],
                    "AccountType" => [
                                "Code" => $supplier->bank->account_type
                    ],
                    "BankAgency" => $supplier->bank->agency,
                    "BankAgencyDigit" => $supplier->bank->agency_digit,
                    "BankAccount" => $supplier->bank->account,
                    "BankAccountDigit" => $supplier->bank->account_digit
                ],
                "Address" => [
                    "ZipCode" => $supplier->address->zipcode,
                    "Street" => $supplier->address->street,
                    "Number" => $supplier->address->number,
                    "Complement" => $supplier->address->complement,
                    "District" => $supplier->address->district,
                    "CityName" => $supplier->address->city,
                    "StateInitials" => $supplier->address->state_code,
                    "CountryName" => $supplier->address->country
                ],
                "MerchantSplit" => [
                    [
                        "PaymentMethodCode" => "1",
                        "IsSubaccountTaxPayer" => false,
                        "Taxes" => [
                            [
                                "TaxTypeName" => "1",
                                "Tax" => "3.50"
                            ]
                        ]
                    ]
                ]
            ];

        $opts = array(
            'http'=>array(
                'method'=>"POST",
                'header'=>"X-API-KEY: $this->apikey\r\n" .
                    "Content-type: application/json\r\n",
                'content'=> json_encode($payload)
            )
        );

        $context = stream_context_create($opts);

        $result = file_get_contents('https://api.safe2pay.com.br/v2/Marketplace/Add', false, $context);

        if ($result === FALSE) {
            return ['status' => 'error', 'message' => 'Não foi possível criar sua conta Safe2Pay, tente novamente.'];
        }else{
            $result = json_decode($result);
            if(isset($result->ResponseDetail) && isset($result->ResponseDetail->Id)){
                $supplier->safe2pay_subaccount_id = $result->ResponseDetail->Id;
                $supplier->save();

                return ['status' => 'success', 'message' => 'Conta Safe2Pay criada com sucesso.'];
            }else{
                if(isset($result->HasError) && $result->Error != ''){
                    return ['status' => 'error', 'message' => $result->Error];
                }else{
                    return ['status' => 'error', 'message' => 'Não foi possível criar sua conta Safe2Pay, tente novamente.'];
                }
            }
        }
    }

    public function tokenizeCard($holder, $cardNumber, $expirationDate, $securityCode){        
        $payload = array(
            'Holder' => $holder,
            'CardNumber' => $cardNumber,
            'ExpirationDate' => $expirationDate,
            'SecurityCode' => $securityCode
        );

        $opts = array(
            'http'=>array(
                'method'=>"POST",
                'header'=>"X-API-KEY: $this->apikey\r\n" .
                        "Content-type: application/json\r\n",
                'content'=> json_encode($payload)
            )
        );

        $context = stream_context_create($opts);

        $result = file_get_contents('https://payment.safe2pay.com.br/v2/token', false, $context);

        if ($result === FALSE) { 
            return FALSE;
        }

        $result = json_decode($result);

        if($result->HasError == true){            
            Log::error('Erro ao inserir cartão: '.$result->Error);
            return FALSE;
        }
        
        if(isset($result->ResponseDetail) && isset($result->ResponseDetail->Token)){
            return $result->ResponseDetail->Token; //caso dê certo, retorna o token na safe2pay
        }
        return FALSE;
    }

    /*
    private function working_example_of_subaccount_creation_payload(){
        $payload_funcionando = [
            "Name" => 'EDUARDO MATTOS',
            "CommercialName" => 'EDUARDO MATTOS 03092928083',
            "Identity" => '27559581000152',
            "ResponsibleName" => 'Eduardo Mattos',
            "ResponsibleIdentity" => '03092928083',
            "Email" => 'duducapao@gmail.com',
            "BankData" => [
                "Bank" => [
                    "Id" => 0,
                    "Code" => '001'
                ],
                "AccountType" => [
                    "Code" => 'CC'
                ],
                "BankAgency" => '3661',
                "BankAgencyDigit" => '7',
                "BankAccount" => '18847',
                "BankAccountDigit" => '6'
            ],
            "Address" => [
                "ZipCode" => '35770000',
                "Street" => 'ROD BR 040 KM 455',
                "Number" => '01',
                "Complement" => 'FAZENDA BALAIOS',
                "District" => 'AREA RURAL',
                "CityName" => 'Caetanopolis',
                "StateInitials" => 'MG',
                "CountryName" => 'Brasil'
            ],
            "MerchantSplit" => [
                [
                    "PaymentMethodCode" => "1",
                    "IsSubaccountTaxPayer" => true,
                    "Taxes" => [
                        [
                            "TaxTypeName" => "1",
                            "Tax" => "5.00"
                        ]
                    ]
                ]
            ]
        ];
    }*/


    function proximoDiaUtil($data, $saida = 'd/m/Y') {
        // Converte $data em um UNIX TIMESTAMP
        $timestamp = strtotime($data);
        // Calcula qual o dia da semana de $data
        // O resultado será um valor numérico:
        // 1 -> Segunda ... 7 -> Domingo
        $dia = date('N', $timestamp);
        
        // Se for sábado (6) ou domingo (7), calcula a próxima segunda-feira
        if ($dia >= 6) {
        $timestamp_final = $timestamp + ((8 - $dia) * 3600 * 24);
        } else {
        // Não é sábado nem domingo, mantém a data de entrada
        $timestamp_final = $timestamp;
        }
        return date($saida, $timestamp_final);
        //echo proximoDiaUtil('2016-09-04');
    }



}


