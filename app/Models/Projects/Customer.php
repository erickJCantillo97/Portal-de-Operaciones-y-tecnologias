<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customers extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['NIT', 'name', 'type', 'email'];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function ships()
    {
        return $this->hasMany(Ship::class);
    }

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function authorizations()
    {
        return $this->hasMany(Authorization::class);
    }

    public function quotes()
    {
        return $this->hasMany(Quote::class);
    }

    public function quotes_authorizations()
    {
        return $this->hasMany(QuotAuthorization::class);
    }
}
