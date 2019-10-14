@component('mail::message', compact('client'))
# {{ trans("auth.mail.{$client->routeName}.label") }}

{{ trans("auth.mail.{$client->routeName}.text") }}

@component('mail::button', ['url' => $client->tokenUrl, 'color' => $client->buttonColor])
{{ trans("auth.mail.{$client->routeName}.button") }}
@endcomponent

{{ trans('messages.link.below') }}
[<small>{{ $client->tokenUrl }}</small>]({{ $client->tokenUrl }})

{{ ucfirst(trans('messages.thanks')) }},<br>
{{ config("client.{$client->ident}.brand") }}
@endcomponent
