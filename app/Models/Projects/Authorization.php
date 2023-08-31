<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Authorization extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = ['project_id', 'contract_id', 'gerencia', 'start_date', 'end_date'];

    public function contracts()
    {
        return $this->belongsTo(Contract::class);
    }

    public function projects()
    {
        return $this->belongsTo(Project::class);
    }
}
