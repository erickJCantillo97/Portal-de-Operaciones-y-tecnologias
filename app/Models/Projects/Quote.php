<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Quote extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['ship_id', 'gerencia', 'cost', 'start_date', 'end_date'];

    public function ships()
    {
        return $this->belongsTo(Ship::class);
    }
}
