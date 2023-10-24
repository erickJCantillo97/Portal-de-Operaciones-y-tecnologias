<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Suggestion extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    protected $guarded = [];


    protected function capture(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => str_replace('//', '/', str_replace('public', 'storage', $value)),
            set: fn ($value) => $value,
        );
    }
}
