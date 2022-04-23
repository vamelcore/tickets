@component('mail::message')
# Hello, {{ $name }}!

@component('mail::panel')
    Your new password: **{{ $password }}**
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
