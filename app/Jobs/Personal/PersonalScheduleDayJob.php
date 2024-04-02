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
use Illuminate\Support\Facades\Log;

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
        // $ScheduleTime = ScheduleTime::create([
        //     'schedule_id' => 275,
        //     'hora_inicio' => '01:00',
        //     'hora_fin' => '02:00'
        // ]);
        // $ScheduleTime->save();
        $collisions = DetailScheduleTime::where('fecha', $this->date)->where('idUsuario', $this->personal_id)->orderBy('horaInicio')->get();
        //validamos si solamente hay 1 colisión
        if (count($collisions) == 1) {
            $horario = $collisions->first();
            //si la unica colisión que hay es igual a la nueva actividad, entonces no se crea ningún schedule_time, sino que se acualiza
            //al  horario completo que se asignó al proyecto.
            if ($horario->idTask == $this->task->id) {
                ScheduleTime::where('id', $horario->idScheduleTime)->update([
                    'hora_inicio' => $this->task->project->shiftObject->startShift,
                    'hora_fin' => $this->task->project->shiftObject->endShift,
                ]);
            } else {
                if (( Carbon::parse($horario->horaIncio)->format('H:i') > Carbon::parse($this->task->project->shiftObject->startShift)->format('H:i')) && (Carbon::parse($horario->horaFin)->format('H:i') <  Carbon::parse($this->task->project->shiftObject->endShift)->format('H:i')) ) {
                    programming(Carbon::parse($this->date)->format('Y-m-d'), 
                    $this->personal_id, 
                    Carbon::parse($this->task->project->shiftObject->startShift)->format('H:i'), 
                    Carbon::parse($horario->horaIncio)->subMinute()->format('H:i'), 
                    $this->task->id, $this->persona->Nombres_Apellidos);

                    programming(Carbon::parse($this->date)->format('Y-m-d'), 
                    $this->personal_id, 
                    Carbon::parse($horario->horaFin)->addMinute()->format('H:i'),
                    Carbon::parse($this->task->project->shiftObject->endShift)->format('H:i'), 
                    $this->task->id, 
                    $this->persona->Nombres_Apellidos);
                }else 
                if((Carbon::parse($horario->horaIncio)->format('H:i')  == Carbon::parse($this->task->project->shiftObject->startShift)->format('H:i')) && (Carbon::parse($this->task->project->shiftObject->endShift)->format('H:i') >Carbon::parse($horario->horaFin)->format('H:i') )){
                    programming(Carbon::parse($this->date)->format('Y-m-d'), 
                    $this->personal_id, 
                    Carbon::parse($horario->horaFin)->addMinute()->format('H:i'), 
                    Carbon::parse($this->task->project->shiftObject->endShift)->format('H:i'), 
                    $this->task->id, $this->persona->Nombres_Apellidos);
                }else
                if((Carbon::parse($horario->horaIncio)->format('H:i')  > Carbon::parse($this->task->project->shiftObject->startShift)->format('H:i')) && (Carbon::parse($this->task->project->shiftObject->endShift)->format('H:i')  == Carbon::parse($horario->horaFin)->format('H:i') )){
                    programming(Carbon::parse($this->date)->format('Y-m-d'), 
                    $this->personal_id, 
                    Carbon::parse($this->task->project->shiftObject->startShift)->format('H:i'), 
                    Carbon::parse($horario->horaInicio)->addMinute()->format('H:i'), 
                    $this->task->id, $this->persona->Nombres_Apellidos);
                }
            }
        } else {
            $startHour = $this->task->project->shiftObject->startShift;
            for ($i = 0; $i < count($collisions); $i++) {
                if(Carbon::parse($startHour)->format('H:i') < Carbon::parse($collisions[$i]->horaInicio)->format('H:i')){
                    programming(Carbon::parse($this->date)->format('Y-m-d'), 
                    $this->personal_id, 
                    Carbon::parse($startHour)->format('H:i'), 
                    Carbon::parse($collisions[$i]->horaInicio)->subMinute()->format('H:i'), 
                    $this->task->id, $this->persona->Nombres_Apellidos);
                }
                $startHour = Carbon::parse($collisions[$i]->horaFin)->addMinute()->format('H:i');
                if ($i == count($collisions)) {
                    if(Carbon::parse($collisions[$i]->horaFin)->format('H:i') < Carbon::parse($this->task->project->shiftObject->startShift)->format('H:i')){
                        programming(Carbon::parse($this->date)->format('Y-m-d'), 
                        $this->personal_id, 
                        Carbon::parse($collisions[$i]->horaFin)->addMinute()->format('H:i'), 
                        Carbon::parse($this->task->project->shiftObject->startShift)->format('H:i'), 
                        $this->task->id, $this->persona->Nombres_Apellidos);
                    }
                }
            }
        }
    }
}
