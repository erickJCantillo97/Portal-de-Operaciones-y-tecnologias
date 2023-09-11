<?php

namespace App\Models\Gantt;

use App\Models\Projects\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $appends  = ['children'];
    protected $guarded = [];

    public function getChildrenAttribute(){
        return Task::where('task_id', '=', $this->id)->get();
    }

    public function project(){
        return $this->belongsTo(Project::class);
    }
}
