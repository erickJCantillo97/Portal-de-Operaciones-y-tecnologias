<?php

namespace App\Models\Gantt;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dependecy extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
}
