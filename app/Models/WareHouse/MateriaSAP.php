<?php

namespace App\Models\WareHouse;

use Illuminate\Database\Eloquent\Model;

class MateriaSAP extends Model
{
    protected $table = 'LISTADO_MATERIALES_View';

    protected $connection = 'sqlsrv_sap';

    protected $guard = 'personal_sap';
}
