<?php

namespace App\Models\SWBS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubSystem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

    public function system(){
        return $this->belongsTo(System::class);
    }
}
