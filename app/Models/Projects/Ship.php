<?php

namespace App\Models\Projects;

use App\Models\Scopes\GerenciaScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Ship extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function typeShip()
    {
        return $this->belongsTo(TypeShip::class, 'type_ship_id')
            ->withDefault(function ($typeShip, $ship) {
                $typeShip->name = 'Sin Clase';
            });
    }

    public function projectsShip()
    {
        return $this->hasMany(ProjectsShip::class);
    }

    protected function file(): Attribute
    {
        return Attribute::make(
            get: fn ($value) =>   str_replace('//', '/', str_replace('public', 'storage', $value)),
            set: fn ($value) => $value,
        );
    }
}
