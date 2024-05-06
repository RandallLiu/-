<?php
namespace App\Services;

use CodeIgniter\Model;
use Firebase\JWT\JWT;

class Auth {

    protected $request;
    protected $secretKey = "";

    function __construct(){
        $this->request = \CodeIgniter\Config\Services::request();//$request;
        $this->secretKey = env("conf.jwt.secretKey");
    }
    // 返回token和refresh_toke
    public function Authorizations($data = [],$is_fresh = false ){
        try {
            $time = time();
            $token = ['iat' => $time,'data' => $data];
            $access_token = $token;
            $access_token['scopes'] = 'access_token';
            $access_token['exp'] = $time + 86400 * 7;

            $refresh_token = $token;
            $refresh_token['scopes'] = 'refresh_toke';
            $refresh_token['exp'] = $time+( 86400 * 15 );
            $jsonList['access_token']  = JWT::encode($access_token,$this->secretKey);
            if( $is_fresh ) $jsonList['refresh_token'] = JWT::encode($refresh_token,$this->secretKey);
        }
        catch (\Exception $ex ){
            $jsonList = ['access_token' => $ex->getMessage()];
        }
        return ($jsonList);
    }

    protected function get_refresh_token($data = []){
        try {
            $time = time();
            $token = [
                'iat' => $time,
                'data' => $data
            ];
            $access_token = $token;
            $access_token['scopes'] = 'access_token';
            $access_token['exp'] = $time + 86400 * 7;

            $jsonList = [
                'access_token'=>JWT::encode($access_token,$this->secretKey),
            ];
        }catch (\Exception $ex ){
            $jsonList = [
                'msg'=>$ex->getMessage(),
            ];
        }

        return ($jsonList);
    }

    // 检测AccessToken是否合法
    public function ckAuthoriztion(){
        $msg = '验签失败';
        try {
            if($this->request->hasHeader('Authorization')) {
                $token = $this->request->getHeader('Authorization');
                if (!$token) return ["code" => false, "msg" => '验证失败'];
                $token = $token->getValue();
                $decoded = JWT::decode($token, $this->secretKey, ['HS256']);
                if ($decoded->scopes == 'access_token') return ["code" => true, "data" => $decoded];
            }
        }
        catch (\Exception $ex){
            return ["code"=>401,"msg"=>$ex->getMessage()];
        }
        return ["code"=>401,"msg"=> $msg];
    }


}