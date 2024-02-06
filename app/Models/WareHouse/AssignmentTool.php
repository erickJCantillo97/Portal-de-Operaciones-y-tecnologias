<?php

namespace App\Models\WareHouse;

use App\Models\Projects\Project;
use App\Models\Scopes\GerenciaScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class AssignmentTool extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $guarded = [];

    protected static function booted(): void
    {
        static::addGlobalScope(new GerenciaScope);
    }

    public function tool()
    {
        return $this->belongsTo(Tool::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
