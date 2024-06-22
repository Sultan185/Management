<?php

namespace App\Models;

use App\Enums\ProcedureEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Procedure extends Model
{
    use HasFactory;

    protected $fillable= [
        'name',
        'notes',
        'asset_id'
    ];
    protected $casts = [
        'name' => ProcedureEnum::class,
    ];
    public function asset(): BelongsTo {
        return $this->belongsTo(Asset::class,'asset_id');
    }
}
