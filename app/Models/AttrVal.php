<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttrVal extends Model
{
    protected $fillable = ['attr_key_id', 'value'];

    public $timestamps = false;

    public function key()
    {
        return $this->belongsTo(AttrKey::class, 'attr_key_id');
    }
}
