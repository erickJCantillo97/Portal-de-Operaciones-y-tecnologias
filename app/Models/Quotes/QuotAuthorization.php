<?php

namespace App\Models\Quote;

use App\Models\Projects\Authorization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class QuotAuthorization extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $table = 'quot_authorizations';

    protected $guarded = [];

    public function authorization()
    {
        return $this->belongsTo(Authorization::class);
    }

    public function quot()
    {
        return $this->belongsTo(Quote::class);
    }
}
