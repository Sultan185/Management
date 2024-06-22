<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable =['name'];

    public function main_category(): BelongsTo{
        return $this->belongsTo(self::class,'parent_id');
    }

    public function sub_categories():HasMany{
        return $this->hasMany(self::class,'parent_id');
    }
    public function assets():HasMany{
        return $this->hasMany(Asset::class);
    }
}
