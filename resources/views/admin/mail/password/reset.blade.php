@component('mail::message')
# Olá!

Você está recebendo este e-mail porque recebemos uma solicitação de redefinição de senha para sua conta.

@component('mail::button', ['url' => $url])
    Redefinir Senha
@endcomponent

Esse link de redefinição de senha expirará em {{ $count }} minutos.

Se você não solicitou a redefinição de senha, nenhuma ação adicional é necessária.

Atenciosamente,
Francês com a Ligia
@endcomponent
