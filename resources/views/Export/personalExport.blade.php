<table>
    <thead>
        <tr>
            <th>Numero de SAP</th>
            <th>Nombre</th>
            <th>Cargo</th>
            <th>Correo</th>
            <th>Oficina</th>
            <th>Costo Hora</th>
            <th>Fecha Fin Contrato</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($personal as $persona)
            <tr>
                <td>{{ $persona['Num_SAP'] }}</td>
                <td>{{ $persona['Nombres_Apellidos'] }}</td>
                <td>{{ $persona['Cargo'] }}</td>
                <td>{{ $persona['Correo'] }}</td>
                <td>{{ $persona['Oficina'] }}</td>
                <td>{{ $persona['Costo_Hora'] }}</td>
                <td>{{ $persona['Fecha_Final'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
