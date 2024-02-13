<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Pep extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $appends = ['peps'];

    public function pep()
    {
        return  $this->belongsTo(Pep::class, 'pep_id');
    }

    



    public function getPepsAttribute()
    {
        return Pep::where('pep_id', '=', $this->id)->get();
    }
}
