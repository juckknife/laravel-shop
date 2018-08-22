<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class ProductSkuResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'title' => $this->title,
            'description' => $this->description,
            'base_price' => $this->base_price,
            'agent_price' => $this->agent_price,
            'price' => $this->price,
            'stock' => $this->stock,
            'attribute' => $this->attr_symbol_path
        ];
    }
}
