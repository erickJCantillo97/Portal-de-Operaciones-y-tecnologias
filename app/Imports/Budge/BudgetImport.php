<?php

namespace App\Imports\Budge;

use App\Models\Project\Grafo;
use App\Models\Project\Operation;
use App\Models\Project\Pep;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BudgetImport implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $pep = Pep::where('grafo_id', $row['estructura_sap'])->first();
            if (!isset($pep)) {
                $pep = Grafo::where('grafo_id', $row['estructura_sap'])->first();
            }
            if (!isset($pep)) {
                $pep = Operation::where('grafo_id', $row['estructura_sap'])->first();
            }
            if (isset($pep)) {
                $pep->materiales_presupestados = $row['presupuesto_materiales'] ?? 0;
                $pep->mano_obra =  $row['presupuesto_mdo'] ?? 0;
                $pep->servicios =  $row['presupuesto_servicios'] ?? 0;
                $pep->save();
            }
        }
    }

    public function chunkSize(): int
    {
        return 300;
    }
}
