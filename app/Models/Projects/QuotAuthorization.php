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

    protected $guarded = ['authorization_id', 'quote_id'];

    public function authorizations()
    {
        return $this->belongsTo(Authorization::class);
    }

    public function quotes()
    {
        return $this->belongsTo(Quote::class);
    }
}
