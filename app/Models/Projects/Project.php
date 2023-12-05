<?php

namespace App\Models\Projects;

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

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function projectShip()
    {
        return $this->hasMany(ProjectShip::class);
    }

    public function status(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value,
            get: fn ($value) => $value ?? 'SIN ESTADO',
        );
    }
}
