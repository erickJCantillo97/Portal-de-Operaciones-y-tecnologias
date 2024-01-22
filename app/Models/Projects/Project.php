<?php

namespace App\Models\Projects;

use App\Models\Scopes\GerenciaScope;
use App\Models\VirtualTask;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Project extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $guarded = [];


    protected static function booted(): void
    {
        static::addGlobalScope(new GerenciaScope);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class)->withDefault(function ($contract) {
            $contract->name = 'Sin Contrato';
        });
    }

    public function projectShip()
    {
        return $this->hasMany(ProjectsShip::class);
    }

    public function status(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value,
            get: fn ($value) => $value ?? 'SIN ESTADO',
        );
    }

    public function tasks()
    {
        return $this->hasMany(VirtualTask::class, 'project_id');
    }
}
