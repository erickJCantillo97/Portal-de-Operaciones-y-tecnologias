<?php

namespace App\Models\Quotes;

use App\Models\Projects\Customer;
use App\Models\Scopes\GerenciaScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteVersion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected static function booted(): void
    {
        static::addGlobalScope(new GerenciaScope);
    }


    protected $guarded = [];

    public function quote()
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function quoteTypeShips()
    {
        return $this->hasMany(QuoteTypeShip::class, 'quote_version_id');
    }
}
