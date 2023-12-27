<?php

namespace App\Models\Projects;

use App\Models\Quotes\Quote;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Authorization extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $guarded = [];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function quote()
    {
        return $this->belongsTo(Quote::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
