<p>Olá {{ $return->supplier_order->order->shop->name }}, o envio do reembolso do pedido {{ $return->supplier_order->order->name }} foi confirmado pelo fornecedor. À seguir, você deve confirmar o recebimento do reembolso em seu painel para finalizar o processo de reembolso. Em caso de dúvidas, entre em contato com nosso suporte.</p>

<p>
    Atenciosamente, <br>
    <b>{{env('APP_NAME')}}</b>
</p>
