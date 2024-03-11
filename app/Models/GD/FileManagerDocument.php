<?php

namespace App\Models\GD;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileManagerDocument extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    protected function filepath(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => '/' . str_replace('//', '/', str_replace('public', 'storage', $value)),
            set: fn ($value) => $value,
        );
    }
}
