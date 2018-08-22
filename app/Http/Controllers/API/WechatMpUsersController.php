<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\BaseController;
use App\Jobs\DecryptUserInfo;
use App\Models\User;
use App\Models\WechatMpUser;
use Cache;
use DB;
use EasyWeChat\MiniProgram\Application;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;
use Log;

/**
 *
 * @package App\Http\Controllers\API
 */
class WechatMpUsersController extends BaseController
{
    private $wechat_app;

    public function __construct(Application $wechat_app)
    {
        $this->wechat_app = $wechat_app;
    }

    /**
     * 微信登录接口
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function auth(Request $request)
    {
        $input = $request->only(['js_code', 'iv', 'encrypted_data']);
        try {
            $session_key_result = Cache::get($input['js_code']);
            if (!$session_key_result) {
                $session_key_result = $this->wechat_app->auth->session($input['js_code']);
                Cache::add($input['js_code'], $session_key_result, 5);
            }
        } catch (\Exception $e) {
            $error_msg = 'js_code[' . $input['js_code'] . '] 换 session key 时出错';
            Log::error($error_msg, ['exception_msg' => $e->getMessage()]);
            throw new \Exception($e->getMessage());
        }

        $openid = $session_key_result['openid'];
        $session_key = $session_key_result['session_key'];


        $wechat_app_user = WechatMpUser::where('openid', $openid)->first();

        DB::beginTransaction();
        if (!$wechat_app_user) {
            $parent_id = $request->get('parent_id') ?? 0;
            try {
                $user = $this->createBaseUser($openid, $parent_id, $shopId);
                $user->account()->create(['amount' => 0]);
                $wechat_app_user = WechatMpUser::create(['user_id' => $user->id, 'openid' => $openid]);
            } catch (QueryException $e) {
                DB::rollBack();
                $error_msg = 'auth 数据库存储用户时发生了些错误';
                Log::error($error_msg, ['exception_msg' => $e->getMessage()]);
                throw new \Exception($e->getMessage());
            }

        }
        // 解密用户详细信息，并修改已保存用户信息
        $this->dispatchNow(new DecryptUserInfo($wechat_app_user->id, $session_key, $input['iv'], $input['encrypted_data'], $shopId));
        DB::commit();
        $auth_token = JWTAuth::fromUser($wechat_app_user->user);

        return $this->success(['auth_token' => $auth_token], '登录成功！');
    }

    /**
     * 创建小程序用户信息之前先创建基础用户信息数据
     *
     * @param $openid
     *
     * @return User
     */
    private function createBaseUser($openid, $parentId, $shopId)
    {
        $data = [
            'name' => $openid,
            'email' => $openid . '@' . 'wechat.app',
            'password' => bcrypt($openid),
            'parent_id' => $parentId,
        ];

        return User::create($data);
    }
}