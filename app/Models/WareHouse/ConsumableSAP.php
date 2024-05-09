<?php

namespace App\Models\WareHouse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class ConsumableSAP extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    protected $table = 'GC_SALIDA_ALMACEN_View';
    protected $connection = 'sqlsrv_sap';
    protected $guard = 'personal_sap';

    protected $guarded = [];
}
