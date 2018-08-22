<?php

namespace App\Http\Resources;

use App\Models\AttrVal;
use Illuminate\Http\Resources\Json\Resource;

class ProductResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $favored = false;
        // 用户未登录时返回的是 null，已登录时返回的是对应的用户对象
        if ($user = $request->user()) {
            // 从当前用户已收藏的商品中搜索 id 为当前商品 id 的商品
            // boolval() 函数用于把值转为布尔值
            $favored = boolval($user->favoriteProducts()->find($this->id));
        }

        $product = [
            'id' => $this->id,
            'title' => $this->title,
            'image' => $this->image,
            'on_sale' => $this->on_sale,
            'rating' => $this->rating,
            'sold_count' => $this->sold_count,
            'review_count' => $this->review_count,
            'base_price' => $this->base_price,
            'agent_price' => $this->agent_price,
            'price' => $this->price,
            'favored' => $favored,
            'skus' => ProductSkuResource::collection($this->whenLoaded('skus')),
        ];

        if ($request->route()->getName() == 'products.show'){
            $attributeIds = [];
            $attribute = [];
            foreach ($this->skus as $sku){
                $attributeIds = array_merge($attributeIds, $sku->attr_symbol_path);
            }
            $attributeIds = array_unique($attributeIds);
            $attrVals = AttrVal::whereIn('id', $attributeIds)->with('key')->get();
            foreach ($attrVals as $val){
                if (isset($attribute[$val->key->id])){
                    $data = $attribute[$val->key->id];
                    $valuelist = $data['values'];
                    $valuelist = array_merge($valuelist, [['id' => $val->id, 'value' => $val->value]]);
                    $data['values'] = $valuelist;
                    $attribute[$val->key->id] = $data;
                } else {
                    $attribute[$val->key->id] = [
                        'name' => $val->key->name,
                        'values' => [
                            [
                                'id' => $val->id,
                                'value' => $val->value,
                            ]
                        ],
                    ];
                }
            }
            $product['attribute'] = collect($attribute)->values();
        }

        return $product;
    }
}
