<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute ;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Labor extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function costoHora(): Attribute
    {
        return Attribute::make(
            set: fn ($value) => $value,
            get: fn($value) => (rand() / getrandmax()) * $value,
        );
    }
}
