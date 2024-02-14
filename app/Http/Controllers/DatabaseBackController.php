<?php

namespace App\Http\Controllers;

use App\Models\Project\Grafo;
use App\Models\Project\Operation;
use App\Models\Project\Pep;
use App\Models\Projects\Ship;
use Illuminate\Support\Facades\DB;

class DatabaseBackController extends Controller
{

    public function getPep()
    {
        Pep::truncate();
        $data =  DB::connection('sqlsrv_anterior')->table('peps')->get();
        foreach ($data as $pep) {
            $casco = DB::connection('sqlsrv_anterior')->table('proyectos')->where('id', $pep->proyecto_id)->first()->casco;
            $project = Ship::where('idHull', $casco)->first()->projectsShip[0]['project_id'];
            $pepPadre = DB::connection('sqlsrv_anterior')->table('peps')->where('id', $pep->pep_id)->first();
            $pep_id = null;
            if ($pepPadre) {
                $pep_id = Pep::where('grafo_id', $pepPadre->grafo_id)->first()->id ?? null;
            }
            Pep::create([
                'project_id' => $project,
                'pep_id' => $pep_id,
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

    public function getGrafos()
    {
        Grafo::truncate();
        $data =  DB::connection('sqlsrv_anterior')->table('grafos')->get();
        foreach ($data as $grafo) {
            $casco = DB::connection('sqlsrv_anterior')->table('proyectos')->where('id', $grafo->proyecto_id)->first()->casco;
            $project = Ship::where('idHull', $casco)->first()->projectsShip[0]['project_id'];
            $pepPadre = DB::connection('sqlsrv_anterior')->table('peps')->where('id', $grafo->pep_id)->first();
            $pep_id = null;
            if ($pepPadre) {
                $pep_id = Pep::where('grafo_id', $pepPadre->grafo_id)->first()->id ?? null;
            }
            Grafo::firstOrCreate([
                'project_id' => $project,
                'pep_id' => $pep_id,
                'identification' => $grafo->identificacion,
                'grafo_id' => $grafo->grafo_id,
                'materials' => $grafo->materiales_presupestados,
                'labor' => $grafo->mano_obra,
                'services' => $grafo->servicios,
                'materials_ejecutados' => $grafo->materiales_ejecutados,
                'labor_ejecutados' => $grafo->mano_obra_ejecutados,
                'services_ejecutados' => $grafo->servicios_ejecutados,
            ]);
        }
    }
    public function getOperations()
    {
        Operation::truncate();
        $data =  DB::connection('sqlsrv_anterior')->table('operacions')->get();
        foreach ($data as $operacion) {
            $casco = DB::connection('sqlsrv_anterior')->table('proyectos')->where('id', $operacion->proyecto_id)->first()->casco;
            $project = Ship::where('idHull', $casco)->first()->projectsShip[0]['project_id'];

            $grafo_padre = DB::connection('sqlsrv_anterior')->table('grafos')->where('id', $operacion->grafo)->first();
            $grafo = null;
            if ($grafo_padre) {
                $grafo = Grafo::where('grafo_id', $grafo_padre->grafo_id)->first()->id ?? null;
            }
            Operation::firstOrCreate([
                'project_id' => $project,
                'grafo' => $grafo,
                'identification' => $operacion->identificacion,
                'grafo_id' => $operacion->grafo_id,
                'materials' => $operacion->materiales_presupestados,
                'labor' => $operacion->mano_obra,
                'services' => $operacion->servicios,
                'materials_ejecutados' => $operacion->materiales_ejecutados,
                'labor_ejecutados' => $operacion->mano_obra_ejecutados,
                'services_ejecutados' => $operacion->servicios_ejecutados,
            ]);
        }
    }
}
