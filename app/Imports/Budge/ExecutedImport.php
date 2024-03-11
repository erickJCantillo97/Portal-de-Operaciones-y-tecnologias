<?php

namespace App\Imports\Budge;

use App\Models\Project\Grafo;
use App\Models\Project\Operation;
use App\Models\Project\Pep;
use App\Models\TypeAccount;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExecutedImport implements ToCollection, WithChunkReading, WithHeadingRow
{
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
        foreach ($collection as $key => $row) {

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
                // dd('No hemos encontrado el grafo: ' . $row['objeto'] . ' En la Estructura');
            }
        }
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
