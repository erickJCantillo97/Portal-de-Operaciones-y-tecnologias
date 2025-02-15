<?php

namespace App\Imports;

use App\Models\Gantt\Task;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UsersImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue
{

    protected $project;
    public function __construct($project)
    {
        $this->project = $project;
    }
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function collection(Collection $rows)
    {
        foreach($rows as $row){
            $tasks = Task::where('project_id', $this->project)->where('id', trim($row['name']))->get();
            foreach($tasks as $task){
                $task->update([
                    'manager' => $row['manager'],
                    'executor' => $row['executor'],
                ]);
            }
        }
    }
    public function chunkSize(): int
    {
        return 1000;
    }






}
