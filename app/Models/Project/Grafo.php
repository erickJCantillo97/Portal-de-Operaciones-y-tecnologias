<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Grafo extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    public $timestamps = false;

    protected $guarded = [];

    protected $appends = ['operaciones'];

    public function getOperacionesAttribute()
    {
        return Operation::where('grafo', '=', $this->id)->get();
    }

    public function materialsEjecutados()
    {
        return Attribute::make(
            get: fn ($value) => $this->getOperacionesAttribute()->sum('materials_ejecutados') +  $value,
        );
    }
    public function laborEjecutados()
    {
        return Attribute::make(
            get: fn ($value) => $this->getOperacionesAttribute()->sum('labor_ejecutados') +  $value,
        );
    }
    public function services_ejecutados()
    {
        return Attribute::make(
            get: fn ($value) => $this->getOperacionesAttribute()->sum('services_ejecutados') +  $value,
        );
    }
}
