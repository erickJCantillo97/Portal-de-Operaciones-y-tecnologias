<?php

namespace App\Imports\Budge;

use App\Models\Project\Grafo;
use App\Models\Project\Operation;
use App\Models\Project\Pep;
use App\Models\WareHouse\Tool;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BudgetImport implements ToCollection,  WithHeadingRow, WithChunkReading, ShouldQueue
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
        // foreach ($rows as $row) {
        //     $tool = Tool::firstorNew([
        //         'code' => $row['code'],
        //     ]);
        //     $tool->serial => $row['serial'];
        //     $tool->responsible_id = $row['responsible_id'];
        //     $tool->category_id = $row['category_id'];
        //     $tool->gerencia = $row['gerencia'];
        //     $tool->value = $row['value'];
        //     $tool->brand = $row['brand'];
        //     $tool->estado = $row['estado'];
        //     $tool->estado_operativo = $row['estado_operativo'];
        //     $tool->save();
        // }
        // dd($rows[0]);
        Validator::make($rows->toArray(), [
            '*.presupuesto_materiales' => 'nullable|numeric',
            '*.presupuesto_mdo' => 'nullable|numeric',
            '*.presupuesto_servicios' => 'nullable|numeric',
            '*.estructura_sap' => 'required',
        ])->validate();

        foreach ($rows as $row) {
            $pep = Pep::where('grafo_id', 'LIKE', $row['estructura_sap'])->first();
            if (!isset($pep)) {
                $pep = Grafo::where('grafo_id', 'LIKE', $row['estructura_sap'])->first();
            }
            if (!isset($pep)) {
                $pep = Operation::where('grafo_id', 'LIKE', $row['estructura_sap'])->first();
            }
            if (isset($pep)) {
                $pep->materials = $row['presupuesto_materiales'] ?? 0;
                $pep->labor =  $row['presupuesto_mdo'] ?? 0;
                $pep->services =  $row['presupuesto_servicios'] ?? 0;
                $pep->save();

                // dd($pep);
            }
        }
    }

    public function chunkSize(): int
    {
        return 300;
    }
}
