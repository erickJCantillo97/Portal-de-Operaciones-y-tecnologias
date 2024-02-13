<?php

namespace App\Http\Controllers;

use App\Models\Project\Pep;
use App\Models\Projects\Ship;
use Illuminate\Support\Facades\DB;

class DatabaseBackController extends Controller
{

    public function getPep()
    {
        $data =  DB::connection('sqlsrv_anterior')->table('peps')->get();
        foreach ($data as $pep) {
            $casco = DB::connection('sqlsrv_anterior')->table('proyectos')->where('id', $pep->proyecto_id)->first()->casco;
            $project = Ship::where('idHull', $casco)->first()->projectsShip[0]['project_id'];
            Pep::create([
                'project_id' => $project,
                'pep_id' => $pep->pep_id,
                'identification' => $pep->identificacion,
                'grafo_id' => $pep->grafo_id,
                'materials' => $pep->materiales_presupestados,
                'labor' => $pep->mano_obra,
                'services' => $pep->servicios,
                'materials_ejecutados' => $pep->materiales_ejecutados,
                'labor_ejecutados' => $pep->mano_obra_ejecutados,
                'services_ejecutados' => $pep->servicios_ejecutados,
            ]);
        }
    }
}
