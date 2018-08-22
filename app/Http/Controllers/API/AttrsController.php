<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Models\AttrKey;
use App\Models\AttrVal;

class AttrsController extends BaseController
{
    public function options()
    {
        $q = request()->get('q');

        $query = AttrVal::query()->where('value', 'like', "%$q%");

        $attrs  = $query->paginate(20, ['id', 'value as text']);

        return \Response::make($attrs);
    }

    public function optinosfromcate(){
        $q = request()->get('q');

        $ids = AttrKey::where('category_id', $q)->get()->pluck('id')->toArray();

        $query = AttrVal::query()->whereIn('attr_key_id', $ids);

        $attrs  = $query->get(['id', 'value as text']);

        return \Response::make($attrs);
    }
}
