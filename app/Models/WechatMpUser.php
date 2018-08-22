<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WechatMpUser extends Model
{
    protected $fillable = ['user_id', 'openid', 'nickname', 'gender', 'city', 'province', 'country', 'avatar_url',
        'union_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
