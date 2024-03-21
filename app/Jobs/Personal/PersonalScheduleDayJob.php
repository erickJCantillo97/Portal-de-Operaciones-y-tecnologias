<?php

namespace App\Jobs\Personal;

use App\Models\ScheduleTime;
use App\Models\Views\DetailScheduleTime;
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
    public $task_id;
    /**
     * Create a new job instance.
     */
    public function __construct($date, $personal_id, $task_id)
    {
        $this->date = $date;
        $this->personal_id = $personal_id;
        $this->task_id = $task_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $horarios = DetailScheduleTime::where('fecha', $this->date)->where('idUsuario', $this->personal_id)->orderBy('horaInicio')->get();

        if (count($horarios) == 1) {
            $horario = $horarios->first();
            if ($horario->idTask == $this->task_id) {
                ScheduleTime::where('id', $horario->idScheduleTime)->update([
                    'hora_inicio' => '07:00:00.0000000',
                    'hora_fin' => '16:30:00.0000000',
                ]);
            }
        }
    }
}
