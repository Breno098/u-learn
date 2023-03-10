@component('mail::message')
# Evento ao vivo cancelado

Prezado aluno, o evento {{ $liveEventName }} foi cancelado!

<br>
{{ config('app.name') }}
@endcomponent
