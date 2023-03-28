<p>Olá {{ $user->name }}, recebemos uma requisição de recuperação de senha no {{env('APP_NAME')}}. Clique no botão abaixo para define uma nova senha.</p>

<p><b>Seu email:</b> {{ $user->email }}</p>

<div style="margin:20px 0px;">
	<a href="{{ $url }}" style="background-color:#1447EF; border-radius:0px; border:1px solid #1141dd; padding:10px 12px; color:white; text-decoration:none">Recuperar Senha</a>
</div>

<p>Caso você não tenha feito a requisição, ignore este e-mail.</p>

<p>
	Atenciosamente, <br>
	<b>{{env('APP_NAME')}}</b>
</p>