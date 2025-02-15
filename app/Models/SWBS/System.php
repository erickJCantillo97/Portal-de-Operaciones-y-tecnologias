<?php

namespace App\Models\SWBS;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class System extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $guarded = [];

    public function constructiveGroupId(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value.'00',
            set: fn ($value) => $value - 1,
        );
    }
}
