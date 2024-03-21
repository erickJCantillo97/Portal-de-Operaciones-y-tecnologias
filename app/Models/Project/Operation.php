<?php

namespace App\Models\Project;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Operation extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    public $timestamps = false;

    protected $casts = [
        'materials_ejecutados' => 'integer',
        'labors_ejecutados' => 'integer',
        'services_ejecutados' => 'integer',
    ];

    protected $guarded = [];
}
