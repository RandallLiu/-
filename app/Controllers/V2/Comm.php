<?php
namespace App\Controllers\V2;

use App\Libraries\LibComp;
use App\Services\sms;
use phpspider\core\db;

class Comm extends BC
{
    function __construct(){}

    function get_specials(){
        $db = new \App\Models\Resource\Special();

        $level1 = [
            "1" => '本科',
            "2" => '专科（高职）'
        ];

        $data = [];
        foreach ( $level1 as $k=>$v ){
            $child = [];
            $level2_data = $db->distinct()->select('level2,level2_name')->where('level1',$k)->findAll();
            foreach ( $level2_data as $l2 ) {
                $child2 = [];
                $level3_data = $db->distinct()->select('level3,level3_name')->where(['level1'=>$k,'level2'=>$l2['level2']])->findAll();
                foreach ( $level3_data as $l3 ) {
                    $child2[] = [
                        "id" => $l3["level3"],
                        "name" => str_ireplace("类",'', $l3["level3_name"])
                    ];
                }
                $child[] = [
                    "id" => $l2["level2"],
                    "name" => str_ireplace("类",'',$l2["level2_name"]),
                    "child" => $child2
                ];
            }

            $data[] = [
                "id" => $k,
                "name" => $v,
                "child" => $child
            ];
        }

        return json_encode($data,JSON_UNESCAPED_UNICODE);
    }

    // 登录页
    public function login(){
        // 提交
        if ($this->request->getMethod() == 'post') {
            $P = $this->U();
            if ( $P['login_type'] == 'sms' ) {
                return $this->_sms($P);
            } else {
                return $this->_passwd($P);
            }
        }
        return $this->render(['view_path' => '/Home/login']);
    }

    // 密码登录
    protected function _passwd($P) {
        log_message('error',json_encode($P)); $db = (new \App\Models\Users());
        if ( $data = $db->where('phone',$P['passwd_phone'])->first() ) {
            log_message('error',json_encode($data));
            if ($data['password'] != md5($P['passwd'])) return $this->setError("登录密码错误!");
            // 设置session
            $this->session->set(["id"=>$data['id'],"name"=>$data['name'],'phone' => $data['phone']]);
            $this->_kemu($data);
            // 获取跳转URL
            $data['url'] = $P['url'] ? base64_decode( $P['url'] ) : '/';
            $data['msg'] = "登录成功";
            return $this->toJson($data);
        }
        return $this->setError("登录失败,请检查手机号是否正确!");
    }

    protected function _sms($P) {
        $sms = new sms();
        // 判断图形验证码
        if (!$P['captcha'] && strtolower($P['captcha']) != strtolower(session('authcode'))) return $this->setError("请填写正确的图形验证码!");
        // 判断手机号是否正确
        if (!ck_mobile( $P['phone'] )) return $this->setError("请填写正确的手机号!");
        // 短信验证码
        $ck = $sms->ck_very_code($P['code'] , $P['phone']);
        if ( !$ck['code'] ) return $this->setError($ck['msg']);
        //
        if ( $data = (new \App\Models\Users())->where('phone',$P['phone'])->first() ) {
            // 保存会话session
            $this->session->set(["id"=>$data['id'],"name"=>$data['name'],'phone' => $data['phone']]);
            // 保存高考信息url
            $this->_kemu($data);
            // 获取跳转URL
            $data['url'] = $P['url'] ? base64_decode( $P['url'] ) : '/';
            //
            $data['msg'] = "登录成功";
            // 返回信息
            return $this->toJson($data);
        }
        return $this->setError("登录失败,请检查手机号是否正确!");
    }

    private function _kemu($data) {
        $kemu = json_decode( $data['kemu'] ,true);
        if ( $data['kemu'] ) {
            $this->session->set('kemu',$kemu);
        }
    }


    // 注册页
    public function reg(){
        // 提交
        if ($this->request->getMethod() == 'post') {
            $P = $this->U(); $sms = new sms();
            // 判断图形验证码
            if (!$P['captcha'] && strtolower($P['captcha']) != strtolower(session('authcode'))) return $this->setError("请填写正确的图形验证码!");
            // 判断手机号是否正确
            if (!ck_mobile( $P['phone'] )) return $this->setError("请填写正确的手机号!");
            // 短信验证码
            $ck = $sms->ck_very_code($P['code'] , $P['phone']);
            if ( !$ck['code'] ) return $this->setError($ck['msg']);
            //密码验证
            if ($P['password'] != $P['repasswd'] ) return $this->setError("两次输入的密码不一致!");
            if (strlen($P['password']) < 6 )return $this->setError("请输入不小到6位登录密码");

            $P['password'] = md5($P['password']);
            // 保存操作
            if ((new \App\Models\Users())->save($P)) {
                return $this->toJson("注册成功, 请登录系统!");
            }
            return $this->setError("保存失败");
        }
        return $this->render(['view_path' => '/Home/reg']);
    }

    // 注册短信
    public function send_sms($type = 'reg'){
        try {
            $P = $this->U(); $db = new \App\Models\Users();

            // 判断难码是否正确
            if ( strtolower(session('authcode')) != strtolower($P['captcha']) ) return $this->setError('验证码错误,请重新输入!');
            // 判断手机号是否正确
            if ( !ck_mobile( $P['phone']) ) return $this->setError('请输入正确的手机号');

            // 判断手机号是否存在
            if ( $db->where('phone',$P['phone'])->first() && $type == 'reg') return $this->setError('发送失败, 注册的手机号已存在');

            if ( $P['phone'] && ck_mobile( $P['phone'] ) ) {
                $sms = new sms();
                $code = rand(100000, 999999);
                // 保存验证码
                $sms->create_very_code( $code,$P['phone'],$this->request );
                // 注册
                $sms_code = 'SMS_56570275';
                if ($type == 'login') $sms_code = 'SMS_56570277';
                // 发送短信
                $sms->send_verify($P['phone'],$sms_code,[$code,'一贸通']);
                return $this->toJson('短信已发送,请注意查收!');
            }
            return $this->setError('发送失败');
        } catch (\Exception $e) {
            return $this->setError($e->getMessage());
        }
    }


}