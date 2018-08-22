<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttrKey extends Model
{
    protected $fillable = ['name', 'category_id'];

    public $timestamps = false;

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function values(){
        return $this->hasMany(AttrVal::class);
    }
}
