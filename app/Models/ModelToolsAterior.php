<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelToolsAterior extends Model
{
    use HasFactory;

    protected $table = 'swbs_base_activities';
    protected $connection = 'sqlsrv_anterior';
    protected $guard = 'personal_sap';
}
