<?php

namespace App\Imports\Budge;

use App\Models\Project\Grafo;
use App\Models\Project\Operation;
use App\Models\Project\Pep;
use App\Models\Projects\Project;
use App\Models\TypeAccount;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExecutedImport implements ToCollection, WithChunkReading, WithHeadingRow
{
    private $project;


    public function __construct($project)
    {
        $this->project = Project::find($project);
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {

        Validator::make($collection->toArray(), [
            '*.objeto' => 'required',
            '*.clase_de_coste' => 'required',
            '*.valor' => 'required|numeric',
        ])->validate();
        $grafos = [];

        foreach ($collection as $key => $row) {
            try {
                $pep = Pep::where('grafo_id', 'LIKE', $row['objeto'])->first();
                if (!isset($pep)) {
                    $pep = Grafo::where('grafo_id', 'LIKE', $row['objeto'])->first();
                }
                if (!isset($pep)) {
                    $operacion = explode(' ', $row['objeto']);
                    $pep = Operation::where('grafo_id', 'LIKE', $operacion[0] . '%')->where('grafo_id', 'LIKE', '%' . $operacion[1])->first();
                }
                if (isset($pep)) {
                    $tipo = TypeAccount::where('account', 'LIKE', $row['clase_de_coste'])->first();
                    if (!isset($tipo)) {
                        addNotification('Error al Subir el Costo de ' . $this->project->name, 'error', 'No hemos encontrado la clase: ' . $row['clase_de_coste']);
                        dd('La clase de Cuenta: ' . $row['clase_de_coste'] . ' No se encuentra en la base de datos');
                    }
                    if ($tipo->class == "MATERIALES") {
                        $pep->materials_ejecutados += $row['valor'];
                    } else if (TypeAccount::where('account', 'LIKE', $row['clase_de_coste'])->first()->class == "MOD") {
                        $pep->labor_ejecutados += $row['valor'];
                    } else {
                        $pep->services_ejecutados += $row['valor'];
                    }
                    $pep->save();
                } else {
                    addNotification('Error al Subir el Costo de ' . $this->project->name, 'error', 'No hemos encontrado el grafo: ' . $row['objeto']);
                    array_push($grafos, 'No hemos encontrado el grafo: ' . $row['objeto'] . ' En la Estructura');
                    break;
                    // dd('No hemos encontrado el grafo: ' . $row['objeto'] . ' En la Estructura');
                }
            } catch (Exception $e) {
                addNotification('Error al Subir el Costo de ' . $this->project->name, 'error', 'Ha Ocurrido un Error Inesperado');
                dd($e);
            }
        }
        Session::put('grafos_errors', $grafos);
        // session('grafos_errors', $grafos);
    }

    public function chunkSize(): int
    {
        return 300;
    }
}
