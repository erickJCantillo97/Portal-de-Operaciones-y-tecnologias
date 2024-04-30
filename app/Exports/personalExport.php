<?php

namespace App\Exports;

use App\Models\Personal\Employee;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class personalExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $personal = Employee::get()->map(function ($person) {
            return [
                'Num_SAP' => (int) $person['Num_SAP'],
                'Fecha_Final' => $person['Fecha_Final'],
                'Costo_Hora' => $person['Costo_Hora'],
                'Correo' => $person['Correo'],
                'Costo_Mes' => $person['Costo_Mes'],
                'Oficina' => $person['Oficina'],
                'Nombres_Apellidos' => $person['Nombres_Apellidos'],
                'Cargo' => $person['Cargo'],
                // 'photo' => User::where('userprincipalname', $person['Correo'])->first()->photo(),
            ];
        });
        return (view('Export/personalExport', [
            'personal' => $personal
        ]));
    }
}
