<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Pep extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $appends = ['peps', 'grafos'];

    public function pep()
    {
        return  $this->belongsTo(Pep::class, 'pep_id');
    }

    public function getPepsAttribute()
    {
        return Pep::where('pep_id', '=', $this->id)->get();
    }

    public function getGrafosAttribute()
    {
        return Grafo::where('pep_id', '=', $this->id)->get();
    }

    public function materialsEjecutados(): Attribute
    {
        return  Attribute::make(
            get: fn ($value) => $this->getGrafosAttribute()->sum('materials_ejecutados') + $this->getPepsAttribute()->sum('materials_ejecutados') +  $value,
            set: fn ($value) => $value,
        );
    }
    public function laborEjecutados(): Attribute
    {
        return  Attribute::make(
            get: fn ($value) => $this->getGrafosAttribute()->sum('labor_ejecutados') + $this->getPepsAttribute()->sum('labor_ejecutados') +  $value,
            set: fn ($value) => $value,
        );
    }
    public function servicesEjecutados(): Attribute
    {
        return  Attribute::make(
            get: fn ($value) => $this->getGrafosAttribute()->sum('services_ejecutados') + $this->getPepsAttribute()->sum('services_ejecutados') +  $value,
            set: fn ($value) => $value,
        );
    }
}
