<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use App\Model\Shop;

class BaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success($result = '', $message = 'success', $code = 200)
    {
        $res = [
            'message' => $message,
            'success' => true,
            'data' => $result
        ];
        return \Response::make($res, $code);
    }

    public function fail($result = '', $message = 'fail', $code = 400)
    {
        $res = [
            'message' => $message,
            'success' => false,
            'data' => $result
        ];
        return \Response::make($res, $code);
    }
}