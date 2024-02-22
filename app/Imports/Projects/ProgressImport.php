<?php

namespace App\Imports\Projects;

use App\Models\Project\ProgressProjectWeek;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProgressImport implements ToCollection, WithHeadingRow
{

    public $project;

    public function __construct($project)
    {
        ini_set('max_execution_time', 0);
        $this->project = $project;
    }
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        Validator::make($collection->toArray(), [
            '*.semana' => 'required|numeric',
            '*.avance_planeado' => 'required|numeric',
            '*.avance_real' => 'nullable|numeric',
        ])->validate();
        foreach ($collection as $key => $row) {
            $avance = ProgressProjectWeek::firstOrNew([
                'week' => $row['semana'],
                'project_id' => $this->project
            ]);
            $avance->planned_progress = ($row['avance_planeado'] ?? 0) * 100;
            $avance->real_progress = ($row['avance_real'] ?? ($avance->real_progress ?? 0)) * 100;
            $avance->save();
        }
    }
}
