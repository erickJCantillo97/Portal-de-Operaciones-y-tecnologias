<?php

namespace App\Models\Gantt;

use App\Models\Projects\Project;
use App\Models\Gantt\Segment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Task extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    protected $casts = [
        'manuallyScheduled' => 'boolean',
        'duration' => 'float'
    ];

    protected $appends = ['children','segments', 'schedulingMode', 'calendar' ];

    protected $guarded = [];

    public function getChildrenAttribute()
    {
        return Task::where('task_id', '=', $this->id)->orderBy('parentIndex')->get();
    }

    public function getCalendarAttribute(){
        return intval($this->calendar_id);
    }

    public function getSchedulingModeAttribute(){
        return "Normal";
    }

    public function getSegmentsAttribute(){
        return Segment::where('task_id', '=', $this->id)->get();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getEventColorAttribute()
    {

        return  strpos($this->name, 'EJECUCION') !== false ? 'green' : '';
    }
    // public function getCalendarIdAttribute()
    // {
    //     return  64;
    // }
    public function executor(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => json_encode($value),
            get: fn ($value) => json_decode($value),
        );
    }
    
    
    public function manager(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => json_encode($value),
            get: fn ($value) => json_decode($value),
        );
    }

    public function scopeForExecutor(Builder $query, $executor)
    {
        $query->where('executor', 'like', '%' . $executor . '%');
    }
}
