<?php

namespace App\Models\Security;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public function name(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value,
            get: fn ($value) => $value,
        );
    }
}
