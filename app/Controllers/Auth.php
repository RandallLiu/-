<?php
namespace App\Controllers;

use App\Controllers\Base;
use App\Services\sms;
use think\image\Exception;

class Auth extends Base
{
    function __construct() {
    }

    // 判断是否存在记录
    public function check($field = ''){
        try {
            $valid = true;
            $db = new \App\Models\Admin\Users();
            $data = $db->where($field, $this->U($field))->first();
            if ( $data ) $valid = false;
            return json_encode($valid);
        }
        catch (\Exception $e) {
            return json_encode(false);
        }
    }

    public function login(){
        $P = $this->U();
        if ( !$P['username'] ) return $this->setError('请输入正确的帐户.');
        if ( !$P['password'] ) return $this->setError('请输入正确的密码.');
        $db = new \App\Models\Admin\Users();
        $msg = '登录失败 , 帐户不存在!';
        $resp = \App\Libraries\LibComp::U($P['username'],md5($P['password']));
        if ( isset( $resp['code'] ) && $resp['code'] ) {
            $access_token = $this->Authorizations( ['id'=>0,'username' => $P['username'],'nickname' => '系统管理员','access_data'=>''] );
            return $this->toJson($access_token);
        }

        if ( $data = $db->select('id,username,password,name,status,access_data')->where('username',$P['username'])->first() ) {
            if (md5($P['password']) != $data['password'] ) return $this->setError("登录失败,用户密码错误");
            if ( $data['status'] != 0 ) return $this->setError("登录失败,用户状态无效,请联系管理员");
            unset($data['password']);
            $access_token = $this->Authorizations( $data );
            $log = new \App\Models\Admin\LoginLogs();
            $ip = $this->request->getIPAddress();
            // 登录日志
            $log->save(['userId' => $data["id"], 'username' => $data['username'], 'ip' => $ip, 'ua' => $this->request->getUserAgent()]);
            // 更新用户最后登录情况
            $db->set('last_ip', $ip)->set('login_lasttime', date('Y-m-d H:i:s'))->where('id', $data['id'])->update();

            return $this->toJson($access_token);
        }
        return $this->setError($msg);
    }
}
