<?php

namespace App\Models;

use App\Models\Gantt\Task;
use App\Models\Personal\Personal;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends = ['is_my_personal'];
    protected $guarded = [];
    protected $casts = [
        'is_my_personal' => 'boolean',
    ];

    public function scheduleTimes()
    {
        return $this->hasMany(ScheduleTime::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function getIsMyPersonalAttribute()
    {
        $personal = getPersonalUser()->pluck('Num_SAP')->toArray();
        return in_array(
            $this->employee_id,
            $personal
        ) ? true : false;
    }
}
