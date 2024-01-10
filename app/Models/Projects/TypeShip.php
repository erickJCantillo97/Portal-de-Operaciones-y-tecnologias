<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class TypeShip extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $guarded = [];

    protected function render(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => str_replace('//', '/', str_replace('public', 'storage', $value)),
            set: fn ($value) => $value,
        );
    }

    public function ships()
    {
        return $this->hasMany(Ship::class, 'type_ship_id');
    }
}
