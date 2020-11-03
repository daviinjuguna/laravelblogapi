@component('mail::message')
# Introduction

This is your six digit number.

@component('mail::button', ['url' => ''])
    {{$pin}}
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
