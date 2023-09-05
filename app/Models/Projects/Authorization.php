<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Authorization extends Model
{
    use HasFactory;
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
