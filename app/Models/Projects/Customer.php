<?php

namespace App\Models\Projects;

use App\Models\Quote\QuotAuthorization;
use App\Models\Quotes\Quote;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Customer extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $guarded = [];

    public function contract()
    {
        return $this->hasMany(Contract::class);
    }

    public function ship()
    {
        return $this->hasMany(Ship::class);
    }

    public function project()
    {
        return $this->hasMany(Project::class);
    }

    public function authorization()
    {
        return $this->hasMany(Authorization::class);
    }

    public function quot()
    {
        return $this->hasMany(Quote::class);
    }

    public function quote_authorization()
    {
        return $this->hasMany(QuotAuthorization::class);
    }

    public function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => strtoupper($value),
            set: fn ($value) => strtolower($value)
        );
    }

    // public function getKeyName()
    // {
    //     return $this->slug;
    // }
}
