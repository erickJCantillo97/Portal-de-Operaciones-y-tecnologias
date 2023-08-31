<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['ship_id', 'name', 'gerencia', 'start_date', 'hoursPerDay', 'daysPerWeek', 'daysPerMonth'];

    public function contracts()
    {
        return $this->belongsTo(Contract::class);
    }
}
