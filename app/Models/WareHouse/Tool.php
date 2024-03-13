<?php

namespace App\Models\WareHouse;

use App\Models\Scopes\GerenciaScope;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Tool extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = ['name', 'assignment_name'];



    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('estado_operativo', '<>', 'BAJA');
    }

    public function asignaciones()
    {
        return $this->HasMany(AssignmentTool::class, 'tool_id');
    }
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function getNameAttribute()
    {
        return $this->category->name;
    }

    public function getAssignmentNameAttribute()
    {
        if ($this->estado == 'ASIGNADO') {
            return AssignmentTool::where('tool_id', $this->id)->orderBy('assigment_date')->first()->employee_name ?? '-';
        }
        return '-';
    }
}
