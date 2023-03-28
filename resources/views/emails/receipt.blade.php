<p>Olá {{ $order->customer->first_name }}! Muito obrigado pela sua confiança em nossa loja, através do botão abaixo você pode efetuar o download da nota fiscal do pedido #{{ $order->id }}.</p>

<div style="margin:20px 0px;">
    <a href="{{ route('site.download_receipt', [$order->customer->id, $receipt->id])  }}" style="background-color:#1447EF; border-radius:0px; border:1px solid #1141dd; padding:10px 12px; color:white; text-decoration:none">Download da Nota Fiscal</a>
</div>

@if($order->shop->phone)
    <p>Em caso de dúvidas, você pode responder este e-mail ou entrar em contato conosco através do número {{ $order->shop->f_phone }}.</p>
@else
    <p>Em caso de dúvidas, você pode responder este e-mail.</p>
@endif

<p>
    Atenciosamente, <br>
    <b>{{ $order->shop->name }}</b>
</p>
