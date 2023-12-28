<?php

namespace App\Models\Quotes;

use App\Models\Projects\Customer;
use App\Models\Scopes\GerenciaScope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteVersion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $appends = ['status', 'get_status'];

    protected  $estados = [
        'Proceso',
        'Entregada',
        'Pendinete por firma',
        'Firmada',
        'No Firmada',
        'Contratada',
    ];


    public function quote()
    {
        return $this->belongsTo(Quote::class, 'quote_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function quoteTypeShips()
    {
        return $this->hasMany(QuoteTypeShip::class, 'quote_version_id');
    }

    public function getGetStatusAttribute()
    {
        return $this->estados[(QuoteStatus::where('quote_version_id', $this->id)->orderBy('fecha', 'DESC')->first()->status ?? 0)];
    }

    public function getStatusAttribute()
    {
        return QuoteStatus::where('quote_version_id', $this->id)->orderBy('fecha', 'DESC')->first()->status ?? 0;
    }
}
