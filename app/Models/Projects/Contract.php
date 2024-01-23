<?php

namespace App\Models\Projects;

use App\Models\Scopes\GerenciaScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Contract extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected static function booted(): void
    {
        static::addGlobalScope(new GerenciaScope);
    }

    protected $guarded = [];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
