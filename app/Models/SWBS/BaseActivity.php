<?php

namespace App\Models\SWBS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseActivity extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guard = [];
}
