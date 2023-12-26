<?php

namespace App\Models\Quotes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Quote extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $guarded = [];


    public function version()
    {
        return $this->belongsTo(QuoteVersion::class, 'current_version_id');
    }
}
