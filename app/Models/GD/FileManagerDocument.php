<?php

namespace App\Models\GD;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileManagerDocument extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
}
