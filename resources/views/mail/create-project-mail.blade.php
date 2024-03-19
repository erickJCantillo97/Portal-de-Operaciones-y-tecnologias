@component('mail::message', ['inner_footer_url' => 'http://topclientes.cotecmar.com', 'inner_footer_text' => 'Plataforma Clientes de COTECMAR'])
# Estimados Señores {{ $customer }}

Es un gusto informarles que se ha creado el proyecto <strong class="primary">{{ $project->name }}</strong> con exito, con la siguiente información 

{{-- Tabla --}}
## Datos del Proyecto
@component('mail::table')
    |  | |
    | ------------- |:-------------:|
    | Contrato | {{ $project->contract->contract_id }} |
    | Fecha Inicio | {{ $project->contract->start_date }} |
    | Fecha Fin | {{ $project->contract->end_date }} |
    | Costo de Venta | {{ number_format($project->cost_sale[0], 0) }} {{ $project->cost_sale[1]}} |
    | Tipo de Venta | {{ $project->type }} |
@endcomponent

@endcomponent
