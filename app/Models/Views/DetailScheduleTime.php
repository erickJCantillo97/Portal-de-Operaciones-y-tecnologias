<?php

namespace App\Models\Views;

use App\Models\Gantt\Task;
use App\Models\Scopes\GerenciaScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailScheduleTime extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new GerenciaScope);
    }

    public function task()
    {
        return $this->belongsTo(Task::class, 'idTask');
    }
}
