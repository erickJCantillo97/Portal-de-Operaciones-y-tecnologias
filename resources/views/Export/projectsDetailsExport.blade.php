<table>
    <thead>
        <tr>
            <th>Casco</th>
            <th>Nombre del Buque</th>
            <th>Clase</th>
            <th>Tipo de Buque</th>
            <th>Tipo de Buque</th>
            <th>Material del Casco</th>
            <th>Eslora</th>
            <th>Manga</th>
            <th>Clado de Diseño</th>
            <th>Puntal</th>
            <th>Full load</th>
            <th>Light ship</th>
            <th>Potencia Total</th>
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
                <td>{{ $ship->typeShip->length }}</td>
                <td>{{ $ship->typeShip->breadth }}</td>
                <td>{{ $ship->typeShip->draught }}</td>
                <td>{{ $ship->typeShip->depth }}</td>
                <td>{{ $ship->typeShip->full_load }}</td>
                <td>{{ $ship->typeShip->light_ship }}</td>
                <td>{{ $ship->typeShip->power_total }}</td>
                <td>{{ $ship->typeShip->velocity }}</td>
                <td>{{ $ship->typeShip->autonomias }}</td>
                <td>{{ $ship->typeShip->autonomy }}</td>
                <td>{{ $ship->typeShip->crew }}</td>
                <td>{{ $ship->typeShip->GT }}</td>
                <td>{{ $ship->typeShip->CGT }}</td>
                <td>{{ $ship->typeShip->bollard_pull }}</td>
                <td>{{ $ship->typeShip->clasification }}</td>
                <td>{{ $ship->projectsShip->first()->project->name }}</td>
                <td>{{ $ship->projectsShip->first()->project->SAP_code }}</td>
                <td>{{ $ship->projectsShip->first()->project->supervisor }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
