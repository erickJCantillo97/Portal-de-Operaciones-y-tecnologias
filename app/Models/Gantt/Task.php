<?php

namespace App\Models\Gantt;

use App\Models\Projects\Project;
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
    ];

    protected $appends = ['children'];

    protected $guarded = [];

    public function getChildrenAttribute()
    {
        return Task::where('task_id', '=', $this->id)->get();
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function getEventColorAttribute()
    {
        
        return  strpos($this->name, 'EJECUCION') !== false ? 'green' : '';
    }
}
