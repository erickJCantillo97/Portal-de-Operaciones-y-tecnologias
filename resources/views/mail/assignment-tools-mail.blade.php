@component('mail::message' , ['inner_footer_url' => 'http://www.top.cotecmar.com'])

## Señor {{$employee}}

le Han asignado las siguientes herramientas
{{--Tabla--}}
## lista de herramimentas:
@component('mail::table')
| Herramienta   | Serial        | Codigo Interno |
| ------------- |:-------------:| --------:|
@foreach ($tools as $tool)
| {{ $tool->name }}  | {{$tool->serial}}      | {{$tool->code}}      |
@endforeach
@endcomponent
@endcomponent
