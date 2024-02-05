@component('mail::message' , ['inner_footer_url' => 'http://www.top.cotecmar.com'])

{{--Tabla--}}
## Equipos Asignados:
@component('mail::table')
| Herramienta   | Serial        | Cantidad |
| ------------- |:-------------:| --------:|
@foreach ($tools as $tool)
| {{ $tool }}  | Centered      | $10      |
@endforeach
| Col 3 is      | Right-Aligned | $20      |
@endcomponent
@endcomponent
