<?php

namespace App\Imports\Budge;

use App\Models\Project\Grafo;
use App\Models\Project\Operation;
use App\Models\Project\Pep;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EstructureImport implements ToCollection, WithChunkReading, WithHeadingRow, ShouldQueue
{
    public $proyecto;

    public function __construct($proyecto)
    {
        ini_set('max_execution_time', 0);
        $this->proyecto = $proyecto;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        Validator::make($rows->toArray(), [
            '*.id' => 'required',
            '*.identificacion' => 'required',
        ])->validate();

        $padre_pep = Pep::firstOrCreate([
            'project_id' => $this->proyecto,
            'identification' => $rows[1]['identificacion'],
            'grafo_id' => $rows[1]['id']
        ])->id;
        $padre = null;
        foreach ($rows as $key => $row) {
            if ($key > 0) {
                if (!is_numeric(str_replace(" ", "", $row['id']))) {
                    $separadoPrimero = explode("/", $row['id']);
                    $separadoSegundo = [];
                    if (count($separadoPrimero) > 1) {
                        $separadoSegundo = explode("-", $separadoPrimero[1]);
                    }
                    if (count($separadoSegundo) == 1) {
                        if (Pep::where('grafo_id', $separadoPrimero[0])->first() == null) {
                            dd($row);
                        }
                        $id = Pep::where('grafo_id', $separadoPrimero[0])->first()->id;

                        $padre_pep = Pep::firstOrCreate([
                            'project_id' => $this->proyecto,
                            'grafo_id' => $row['id'],
                        ]);
                        $padre_pep->identification = $row['identificacion'];
                        $padre_pep->pep_id = $id;
                        $padre_pep->save();
                        $padre_pep = $padre_pep->id;
                    } else if (count($separadoSegundo) == 2) {
                        $id = (Pep::where('grafo_id', $separadoPrimero[0] . "/" . $separadoSegundo[0])->first() != null) ? Pep::where('grafo_id', $separadoPrimero[0] . "/" . $separadoSegundo[0])->first()->id : null;
                        $padre_pep = Pep::firstOrCreate([
                            'project_id' => $this->proyecto,
                            'pep_id' => $id,
                            'grafo_id' => $row['id'],
                        ]);
                        $padre_pep->identification = $row['identificacion'];
                        $padre_pep->save();
                        $padre_pep = $padre_pep->id;
                    } else if (count($separadoSegundo) == 3) {
                        $id = Pep::where('grafo_id', $separadoPrimero[0] . "/" . $separadoSegundo[0] . "-" . $separadoSegundo[1])->first();
                        $padre_pep = Pep::firstOrCreate([
                            'project_id' => $this->proyecto,
                            'pep_id' => isset($id) ? $id->id : null,
                            'grafo_id' => $row['id'],
                        ]);
                        $padre_pep->identification = $row['identificacion'];
                        $padre_pep->save();
                        $padre_pep = $padre_pep->id;
                    }
                } else if (str_replace(" ", "", $row['id']) < 999999) {
                    $padre = Grafo::firstOrCreate([
                        'project_id' => $this->proyecto,
                        'grafo_id' => $row['id'],
                    ]);
                    $padre->identification = $row['identificacion'];
                    $padre->pep_id = $padre_pep;
                    $padre->save();
                    $padre = $padre->id;
                } else {
                    $operacion = Operation::firstOrNew([
                        'project_id' => $this->proyecto,
                        'grafo_id' => $row['id'],
                    ]);
                    $operacion->identification = $row['identificacion'];
                    $operacion->grafo = $padre;
                    $operacion->save();
                }
            }
        }
    }

    public function chunkSize(): int
    {
        return 300;
    }
}
