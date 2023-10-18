<?php

namespace App\Models;

use App\Models\Gantt\Task;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function scheduleTimes(){
        return $this->hasMany(ScheduleTime::class);
    }

    public function task(){
        return $this->belongsTo(Task::class, );
    }
}
