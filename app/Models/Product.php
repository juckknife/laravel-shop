<?php

namespace App\Models;

use Illuminate\Support\Str;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'description',
        'image',
        'on_sale',
        'rating',
        'sold_count',
        'review_count',
        'price',
        'base_price',
        'agent_price'
    ];

    protected $casts = [
        'on_sale' => 'boolean',
        'price' => 'float',
        'base_price' => 'float',
        'agent_price' => 'float'
    ];

    // 与商品SKU关联
    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    // 商品详情关联
    public function detail()
    {
        return $this->hasOne(ProductDetail::class);
    }

    //类目
    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute()
    {
        // 如果 image 字段本身就已经是完整的 url 就直接返回
        if (Str::startsWith($this->attributes['image'], ['http://', 'https://'])) {
            return $this->attributes['image'];
        }
        return \Storage::disk('public')->url($this->attributes['image']);
    }
}
