<?php

namespace App\Models\Quotes;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class QuoteStatus extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $guarded = [];

    protected $appends = ['get_status'];

    protected  $estados = [
        'Proceso',
        'Entregada',
        'Pendiente por firma',
        'Firmada',
        'No Firmada',
        'Contratada',
    ];


    public function Quote()
    {
        return $this->belongsTo(QuoteVersion::class, 'quote_version_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getGetStatusAttribute()
    {
        return $this->estados[$this->status];
    }
}
