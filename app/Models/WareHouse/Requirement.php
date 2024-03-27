<?php

namespace App\Models\WareHouse;

use App\Models\Projects\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Requirement extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $guarded = [];

    public function getConsevutivoAttribute()
    {
        return $this->type . '-' . $this->project();
    }

    public function consecutive(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value,
            get: fn ($value) => $this->type . '-' . $this->project->projectShip->first()->ship->idHull . '-' . $this->bloque . '-' . $this->sistema_grupo . '-' . str_pad(
                $value,
                5,
                '0',
                STR_PAD_LEFT
            ),
        );
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
