<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $appends = ['employee'];

    public function getEmployeeAttribute(){
        return searchEmpleados('Num_SAP' , $this->employee_id)->first();
    }

    public function scheduleTimes(){
        return $this->hasMany(ScheduleTime::class);
    }
}
