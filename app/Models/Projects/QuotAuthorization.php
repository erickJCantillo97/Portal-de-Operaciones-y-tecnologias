<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuotAuthorization extends Model
{
    use HasFactory;
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
