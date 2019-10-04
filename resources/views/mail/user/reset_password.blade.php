@component('mail::message', compact('appName', 'appUrl'))
# {{ trans('auth.email.reset.password.label') }}

{{ trans('auth.email.reset.password.text') }}

@component('mail::button', ['url' => $appUrlReset])
{{ trans('auth.email.confirm') }}
@endcomponent

{{ ucfirst(trans('messages.thanks')) }},<br>
{{ trans('app.brand.magicweb') }}
@endcomponent
