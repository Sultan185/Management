<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'barcode_number',
        'location',
        'responsible',
        'purchase_information',
        'purchase_date',
        'additional_information',
        'category_id'
    ];

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class,'category_id');
    }
    public function procedures() : HasMany{
        return $this->hasMany(Procedure::class);
    }
}
