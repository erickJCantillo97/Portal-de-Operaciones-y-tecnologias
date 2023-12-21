<?php

namespace App\Models\Projects;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteTypeShip extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
}
