<?php

namespace App\Models;

use App\Models\Projects\Project;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VirtualTask extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tasks';

    protected $guarded = [];

    public function project(){
        return $this->belongsTo(Project::class);
    }
}
