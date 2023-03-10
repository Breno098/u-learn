@component('mail::message')
# Olá!

Você está recebendo este e-mail porque seu email acabou de ser cadastrado a nossa plataforma.

@component('mail::button', ['url' => $url])
    Defina sua senha
@endcomponent

Esse link para definição de senha expirará em {{ $count }} minutos.

Se não foi você que realizou o cadastro, nenhuma ação adicional é necessária.

Atenciosamente,
Francês com a Ligia
@endcomponent
