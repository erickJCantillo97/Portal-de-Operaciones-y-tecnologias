<table>
    <thead>
        <tr>
            <th>Casco</th>
            <th>Nombre del Buque</th>
            <th>Clase</th>
            <th>Tipo de Buque</th>
            <th>Material del Casco</th>
            <th>Eslora</th>
            <th>Manga</th>
            <th>Clado de Diseño</th>
            <th>Puntal</th>
            <th>Full load</th>
            <th>Light ship</th>
            <th>Potencia Total</th>
            <th>Velocidasd Maxima</th>
            <th>Autonomia</th>
            <th>Alcance</th>
            <th>Tripulacion Maxima</th>
            <th>GT</th>
            <th>CGT</th>
            <th>Bollard pull</th>
            <th>clasificación</th>
            <th>Projecto</th>
            <th>Codigo SAP</th>
            <th>Gerente</th>
            <th>Tipo de Proyecto</th>
            <th>Estado del Proyecto</th>
            <th>Alcance del Proyecto</th>
            <th>Precio de Venta</th>
            <th>Contrato</th>
            <th>Objeto</th>
            <th>Tipo de Venta</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Estado del Contrato</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($ships as $ship)
            <tr>
                <td>{{ $ship->idHull }}</td>
                <td>{{ $ship->name }}</td>
                <td>{{ $ship->typeShip->name }}</td>
                <td>{{ $ship->typeShip->type }}</td>
                <td>{{ $ship->typeShip->hull_material }}</td>
                <td>{{ $ship->typeShip->length }} m</td>
                <td>{{ $ship->typeShip->breadth }} m</td>
                <td>{{ $ship->typeShip->draught }}</td>
                <td>{{ $ship->typeShip->depth }}</td>
                <td>{{ $ship->typeShip->full_load }}</td>
                <td>{{ $ship->typeShip->light_ship }}</td>
                <td>{{ $ship->typeShip->power_total }} Kw</td>
                <td>{{ $ship->typeShip->velocity }} nudos</td>
                <td>{{ $ship->typeShip->autonomias }} dias</td>
                <td>{{ $ship->typeShip->autonomy }}</td>
                <td>{{ $ship->typeShip->crew }} personas</td>
                <td>{{ $ship->typeShip->GT }} Ton</td>
                <td>{{ $ship->typeShip->CGT }} Ton</td>
                <td>{{ $ship->typeShip->bollard_pull }}</td>
                <td>{{ $ship->typeShip->clasification }}</td>
                <td>{{ $ship->projectsShip->first()->project->name ?? '' }}</td>
                <td>{{ $ship->projectsShip->first()->project->SAP_code ?? '' }}</td>
                <td>{{ $ship->projectsShip->first()->project->supervisor ?? '' }}</td>
                <td>{{ $ship->projectsShip->first()->project->type ?? '' }}</td>
                <td>{{ $ship->projectsShip->first()->project->status ?? '' }}</td>
                <td>{{ $ship->projectsShip->first()->project->scope ?? '' }}</td>
                <td>{{ $ship->projectsShip->first()->project->cost_sale[0] ?? '' }}</td>
                <td>{{ $ship->projectsShip->first()->project->contract->contract_id ?? '' }}</td>
                <td>{{ $ship->projectsShip->first()->project->contract->subject ?? '' }}</td>
                <td>{{ $ship->projectsShip->first()->project->contract->type_of_sale ?? '' }}</td>
                <td>{{ $ship->projectsShip->first()->project->contract->start_date ?? '' }}</td>
                <td>{{ $ship->projectsShip->first()->project->contract->end_date ?? '' }}</td>
                <td>{{ $ship->projectsShip->first()->project->contract->state ?? '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
