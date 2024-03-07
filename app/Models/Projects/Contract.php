<?php

namespace App\Models\Projects;

use App\Models\Quotes\QuoteVersion;
use App\Models\Scopes\GerenciaScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Contract extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;

    protected static function booted(): void
    {
        static::addGlobalScope(new GerenciaScope);
    }

    protected $guarded = [];

    protected $appends = ['total_cost', 'project_count'];

    // public function customer()
    // {
    //     return $this->belongsTo(Customer::class);
    // }

    public function quote()
    {
        return $this->belongsTo(QuoteVersion::class, 'quote_id');
    }

    public function getTotalCostAttribute()
    {
        return [$this->quote->total_cost ?? 0, isset($this->quote->coin) ?  $this->quote->coin : 'COP'];
    }
    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    public function getProjectCountAttribute()
    {
        return Project::where('contract_id', $this->id)->count();
    }
}
