<?php

namespace App\Models\Personal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Employee extends Model
{
    public $timestamps = false;
    protected $guarded = [];

    protected $appends = [
        'short_name',
    ];

    public function getShortNameAttribute()
    {
        return explode(' ', $this->employee_name)[0] . ' ' . explode(' ', $this->employee_name)[count(explode(' ', $this->employee_name)) - 2 > -1 ? count(explode(' ', $this->employee_name)) - 2 : 0];
    }
}
