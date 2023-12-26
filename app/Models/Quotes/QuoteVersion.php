<?php

namespace App\Models\Quotes;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QuoteVersion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];
}
