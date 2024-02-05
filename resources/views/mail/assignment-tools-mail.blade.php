@component('mail::message')
# Introduction

The body of your message.


<img src="https://minciencias.gov.co/sites/default/files/cotecmar.png" class="logo" alt="Laravel Logo">

@component('mail::button', ['url' => '', 'color' => 'red', 'align' => 'left'], )
    <i>Ingresar</i>
@endcomponent
@foreach ($tools as $tool)
@component('mail::panel')
    Equipo: {{$tool}}
@endcomponent
@endforeach

## Equipos:

@component('mail::table')
| Laravel       | Table         | Example  |
| ------------- |:-------------:| --------:|
| Col 2 is      | Centered      | $10      |
| Col 3 is      | Right-Aligned | $20      |
@endcomponent


@component('mail::subcopy')
    This is a subcopy component
@endcomponent


Para más información visitar: <br>
{{ config('app.name') }}
@endcomponent
