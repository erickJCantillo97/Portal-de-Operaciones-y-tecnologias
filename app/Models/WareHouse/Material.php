<?php

namespace App\Models\WareHouse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Material extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected $guarded = [];
    public static $estado = [
        'DISPONIBLE GECON' => 1,
        'DISPONIBLE SAP' => 2,
        'DISPONIBLE SAP CONSIGNACION' => 3,
        'DISPONIBLE SAP INHOUSE' => 6,
        'RETAL' => 4,
        'PENDIENTE' => 0,
    ];

    public static $unidad = [
        'UNIDAD' => 1,
        'METROS' => 2,
        'PIES' => 3,
        'METRO' => 4,
    ];
}
