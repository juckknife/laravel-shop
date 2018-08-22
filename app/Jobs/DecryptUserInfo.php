<?php

namespace App\Jobs;

use App\Models\WechatMpUser;
use EasyWeChat\MiniProgram\Encryptor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DecryptUserInfo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $wechat_app_user_id;
    private $session_key;
    private $vi;
    private $encrypted_data;
    private $shop_id;

    /**
     * DecryptUserInfo constructor.
     * @param $wechat_app_user_id
     * @param $session_key
     * @param $vi
     * @param $encrypted_data
     */
    public function __construct($wechat_app_user_id, $session_key, $vi, $encrypted_data, $shop_id)
    {
        $this->wechat_app_user_id = $wechat_app_user_id;
        $this->session_key = $session_key;
        $this->vi = $vi;
        $this->encrypted_data = $encrypted_data;
        $this->shop_id = $shop_id;
    }

    /**
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public function handle()
    {
        $encrytor = new Encryptor($this->session_key, $this->vi, $this->encrypted_data);
        $decrypted_data = $encrytor->decryptData($this->session_key, $this->vi, $this->encrypted_data);
        if ($decrypted_data) {
            // 修改用户表中对应用户的 name，创建时name 默认值是用户的 openid
            $openid = $decrypted_data['openId'];
            $wechatMpUser = WechatMpUser::where('openid', $openid)->first();
            $user = $wechatMpUser->user;
            // 填充微信用户信息
            $wechatMpUser->update([
                'shop_id' => $this->shop_id,
                'openid' => $openid,
                'nickname' => $decrypted_data['nickName'],
                'gender' => $decrypted_data['gender'],
                'city' => $decrypted_data['city'],
                'province' => $decrypted_data['province'],
                'country' => $decrypted_data['country'],
                'avatar_url' => $decrypted_data['avatarUrl'],
                'union_id' => isset($decrypted_data['unionId']) ? $decrypted_data['unionId'] : '',
            ]);
            $user->name = $wechatMpUser->nickname;
            $user->avatar = $wechatMpUser->avatar_url;
            $user->save();
            //关联微信公众号用户
//            if (isset($decrypted_data['unionId']) && $decrypted_data['unionId']) {
//                $wxUser = WxUser::where('union_id', $decrypted_data['unionId'])->first();
//                if ($wxUser) {
//                    $wxUser->user_id = $wechatMpUser->user_id;
//                    $wxUser->save();
//                }
//            }
        }
    }
}
