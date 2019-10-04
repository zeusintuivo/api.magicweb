@component('mail::message', compact('appName', 'appUrl'))
# {{ trans('auth.email.verification.label') }}

{{ trans('auth.email.verification.text') }}

@component('mail::button', ['url' => $appUrlVerify])
{{ trans('auth.email.confirm') }}
@endcomponent

{{ ucfirst(trans('messages.thanks')) }},<br>
{{ trans('app.brand.magicweb') }}
@endcomponent
