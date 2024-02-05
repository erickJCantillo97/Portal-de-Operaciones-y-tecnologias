@component('mail::message' , ['inner_footer_url' => 'http://top.cotecmar.com'])

## Señor {{$employee}}

Le han asignado las siguientes herramientas
{{--Tabla--}}
## Lista de herramientas:
@component('mail::table')
| Herramienta   | Serial        | Codigo Interno |
| ------------- |:-------------:| --------:|
@foreach ($tools as $tool)
| {{ $tool->name }}  | {{ $tool->serial }}      | {{ $tool->code }}      |
@endforeach
@endcomponent
@endcomponent
