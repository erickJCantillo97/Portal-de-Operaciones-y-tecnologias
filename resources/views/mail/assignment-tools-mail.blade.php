@component('mail::message', ['inner_footer_url' => 'http://www.top.cotecmar.com'])
# Señor {{ $employee }}

Se acaba de registrar una asignación de equipos y/o herramientas a su nombre el dia
{{ $date }}
{{-- Tabla --}}
## Lista de herramimentas
@component('mail::table')
    | Herramienta | Serial | Codigo Interno |
    | ------------- |:-------------:| --------:|
    @foreach ($tools as $tool)
        | {{ $tool->name }} | {{ $tool->serial }} | {{ $tool->code }} |
    @endforeach
@endcomponent

<p class="recommendation">
    Recuerde que el usuario con herramientas asignados es el responsable de su correcta operación y preservación. Debe
    realizar una inspección completa antes de su utilización garantizando una condición segura, así como realizar el
    mantenimiento preventivo de nivel 1, es decir limpieza, engrase y ajuste. Ante novedades o niveles de mantenimiento
    mayor, acuda al área de mantenimiento o almacén.
<p>

@endcomponent
