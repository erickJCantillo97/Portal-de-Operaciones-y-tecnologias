<?php

namespace App\Models\WareHouse;

use App\Models\Scopes\GerenciaScope;
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

    protected $appends = ['name'];

    protected static function booted(): void
    {
        static::addGlobalScope(new GerenciaScope);
    }

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

    public function getNameAttribute()
    {
        return $this->category->name;
    }
}
