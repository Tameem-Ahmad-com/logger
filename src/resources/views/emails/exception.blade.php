@component('mail::message')
# Hi,

You are receiving this email because an error occured on production server.
<br>
<h3>Here is error details:</h3>
 
    {{$exception}}
<br>
@component('mail::button', ['url' => config('app.url'))])
Click Here
@endcomponent

If you did not request a signup , no further action is required.

Thanks,
{{ config('app.name') }}
@endcomp