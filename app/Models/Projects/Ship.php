<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Ship extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function ship()
    {
        return $this->belongsTo(Ship::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    protected function file(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => str_replace('public', 'storage', $value),
            set: fn ($value) => $value,
        );
    }
}
