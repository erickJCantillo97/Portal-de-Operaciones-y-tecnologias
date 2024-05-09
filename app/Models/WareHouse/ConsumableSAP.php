<?php

namespace App\Models\WareHouse;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConsumableSAP extends Model
{
    use HasFactory;

    protected $table = 'GC_SALIDA_ALMACEN_View';

    protected $connection = 'sqlsrv_sap';

    protected $guard = 'personal_sap';

    protected $guarded = [];
}
