<?php

namespace App\Models\Quotes;

use App\Models\Comment;
use App\Models\Projects\Customer;
use App\Models\Scopes\GerenciaScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteVersion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
    protected $appends = ['status', 'get_status', 'status_date', 'consecutive', 'total_cost'];

    protected $estados = [
        'Proceso',
        'Entregada',
        'Pendiente por Firma',
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

    public function getConsecutiveAttribute()
    {
        return str_pad($this->quote->consecutive ?? 1, 3, 0, STR_PAD_LEFT) . '-' . $this->version . '-' . Carbon::parse($this->expeted_answer_date)->format('Y');
    }

    public function getGetStatusAttribute()
    {
        return $this->estados[(QuoteStatus::where('quote_version_id', $this->id)->orderBy('fecha', 'DESC')->first()->status ?? 0)];
    }

    public function getStatusAttribute()
    {
        return QuoteStatus::where('quote_version_id', $this->id)->orderBy('fecha', 'DESC')->first()->status ?? 0;
    }

    public function getStatusDateAttribute()
    {
        return QuoteStatus::where('quote_version_id', $this->id)->orderBy('fecha', 'DESC')->first()->fecha ?? '2023-05-02';
    }
    public function getTotalCostAttribute()
    {
        return [$this->quoteTypeShips->sum('price_before_iva_original'), $this->coin];
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }
}
