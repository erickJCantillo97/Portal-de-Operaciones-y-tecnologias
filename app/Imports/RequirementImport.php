<?php

namespace App\Imports;

use App\Models\Projects\Project;
use App\Models\WareHouse\Material;
use App\Models\WareHouse\MaterialRequirement;
use App\Models\WareHouse\Requirement;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class RequirementImport implements ToCollection, WithHeadingRow
{

    public $data;
    public $requerimiento;
    public function __construct($data)
    {
        $this->data = collect($data);
        $this->requerimiento = Requirement::where('project_id', $data['project_id'])
            ->max('id') ?? 0;
        // dd($this->requerimiento);
    }
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collections)
    {
        Validator::make($collections->toArray(), [
            '*.descripcion' => 'required',
            '*.codigo_material' => 'nullable',
            '*.codigo_proveedor' => 'nullable',
            '*.unidad' => 'nullable',
            '*.cantidad' => 'required|numeric',
            '*.tipo_requerimiento' => 'required'
        ])->validate();
        foreach ($collections as $key => $collection) {
            $material = Material::firstOrNew([
                'description' => $collection['descripcion'],
                'code' => $collection['codigo_material'],
            ]);
            $material->code_proveedor = $collection['codigo_proveedor'];
            $material->unit = Material::$unidad[$collection['unidad']] ?? 0;
            $material->save();

            $requirement = Requirement::firstOrCreate([
                'consecutive' => $this->requerimiento + 1,
                'project_id' => $this->data['project_id'],
                'user_id' => auth()->user()->id,
                'document' => $this->data['document'],
                'note' => $this->data['note'] ?? '',
                'sistema_grupo' => $this->data['sistema_grupo'],
                'bloque' => $this->data['bloque'],
                'preeliminar_date' => Carbon::now(),
                'type' => strtolower($collection['tipo_requerimiento']) == 'i' ? 'RI' : 'RF',
            ])->id;
            MaterialRequirement::create([
                'requirement_id' => $requirement,
                'material_id' => $material->id,
                'count' => $collection['cantidad'],
                'status' => 0,
                'unit' =>  Material::$unidad[$collection['unidad']] ?? 0,
            ]);
        }
    }
}
