<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Models\Category;

class CategoriesController extends BaseController
{
    public function options()
    {
        $categorys = Category::query()->orderBy('order')->get();

        $res = [];

        foreach ($categorys as $category){
            $data = [
                'id' => $category->id,
                'text' => $category->title
            ];
            $res[] = $data;
        }

        return \Response::make($res);
    }
}
