<?php

namespace App\Jobs\Personal;

use App\Models\Gantt\Task;
use App\Models\Personal\Employee;
use App\Models\Personal\Personal;
use App\Models\Schedule;
use App\Models\ScheduleTime;
use App\Models\Views\DetailScheduleTime;
use App\Models\VirtualTask;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PersonalScheduleDayJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $date;
    public $personal_id;
    public $persona;
    public $task;

    /**
     * Create a new job instance.
     */
    public function __construct($date, $personal_id, $task_id)
    {
        $this->date = $date;
        $this->personal_id = $personal_id;
        $this->persona = Employee::where('Num_SAP', $personal_id)->first();
        $this->task =  VirtualTask::find($task_id);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $horarios = DetailScheduleTime::where('fecha', $this->date)->where('idUsuario', $this->personal_id)->orderBy('horaInicio')->get();

        if (count($horarios) == 1) {
            $horario = $horarios->first();
            if ($horario->idTask == $this->task->id) {
                ScheduleTime::where('id', $horario->idScheduleTime)->update([
                    'hora_inicio' => $this->task->project->shiftObject->startShift,
                    'hora_fin' => $this->task->project->shiftObject->endShift,
                ]);
            } else {
                if ($horarios[0]->hora_incio > $this->task->project->shiftObject->startShift) {
                }
            }
        } else {
            for ($i = 0; $i < count($horarios); $i++) {
                if ($i == 0) {
                    if ($horarios[0]->hora_incio > $this->task->project->shiftObject->startShift) {
                        if ($horarios[0]->idTask == $this->task->id) {
                            ScheduleTime::where('id', $horarios[0]->idScheduleTime)->update([
                                'hora_inicio' => $this->task->project->shiftObject->startShift,
                            ]);
                        } else {
                            programming(Carbon::parse($this->date)->format('Y-m-d'), $this->personal_id, Carbon::parse($this->task->project->shiftObject->startShift)->format('H:i'), $horarios[0]->hora_incio, $this->task->id, $this->persona->Nombres_Apellidos);
                        }
                    }
                }
            }
        }
    }
}
