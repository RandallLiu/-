<?php
namespace App\Services;

use CodeIgniter\Model;
use CodeIgniter\HTTP\RequestInterface;

class sms {
    protected   $SignName = '';

    protected  $AccessKeyId = '';

    protected  $SecretKey = '';

    protected  $template = [
        'SMS_56570279'=>['param'=>['code','product'],'name'=>'身份验证验证码'],    //验证码${code}，您正在进行${product}身份验证，打死不要告诉别人哦！
        'SMS_56570277'=>['param'=>['code','product'],'name'=>'登录确认验证码'],    //验证码${code}，您正在登录${product}，若非本人操作，请勿泄露。
        'SMS_56570276'=>['param'=>['code','product'],'name'=>'登录异常验证码'],    //验证码${code}，您正尝试异地登录${product}，若非本人操作，请勿泄露。
        'SMS_56570275'=>['param'=>['code','product'],'name'=>'用户注册验证码'],    //验证码${code}，您正在注册成为${product}用户，感谢您的支持！
        'SMS_56570274'=>['param'=>['code','product'],'name'=>'活动确认验证码'],    //验证码${code}，您正在参加${product}的${item}活动，请确认系本人申请。
        'SMS_56570273'=>['param'=>['code','product'],'name'=>'修改密码验证码'],    //验证码${code}，您正在尝试修改${product}登录密码，请妥善保管账户信息。
        'SMS_56570272'=>['param'=>['code','product'],'name'=>'信息变更验证码'],    //验证码${code}，您正在尝试变更${product}重要信息，请妥善保管账户信息。
        'SMS_57190146'=>['param'=>['code'],'name'=>'支付验证码'],                 //付款验证码为${code},请在15分钟内使用.
    ];

    protected $tempcode;

    public function __construct() {
        //$this->tempcode = $code;
    }

    /**
     * 签名
     *
     * @param  $parameters
     * @param  $accessKeySecret
     * @return string
     */

    private function computeSignature($parameters, $accessKeySecret) {
        ksort ( $parameters );
        $canonicalizedQueryString = '';
        foreach ( $parameters as $key => $value ) {
            $canonicalizedQueryString .= '&' .rawurlencode($key) . '=' . rawurlencode( $value );
        }
        $stringToSign = 'GET&%2F&' .rawurlencode( substr ( $canonicalizedQueryString, 1) );
        $signature = base64_encode ( hash_hmac ( 'sha1', $stringToSign, $accessKeySecret . '&', true ) );
        return $signature;
    }

    /**
     * 发送验证码 https://help.aliyun.com/document_detail/44364.html?spm=5176.doc44368.6.126.gSngXV
     *
     * @param $mobile
     * @param $code
     * @param $arr
     *
     */
    public function  send_verify($mobile,$code,$arr) {
        $params = array (
            // 公共参数
            'SignName' => $this->SignName,
            'Format' => 'JSON',
            'Version' => '2016-09-27',
            'AccessKeyId' =>$this->AccessKeyId,
            'SignatureVersion' => '1.0',
            'SignatureMethod' => 'HMAC-SHA1',
            'SignatureNonce' => uniqid (),
            'Timestamp' => gmdate ( 'Y-m-d\TH:i:s\Z' ),
            // 接口参数
            'Action' => 'SingleSendSms',
            'TemplateCode' => $code,
            'RecNum' => $mobile,
            'ParamString' =>''
        );

        $tm = $this->template[$code];
        $params['ParamString'] = '{';
        foreach($tm['param'] as $key=>$val){
            $params['ParamString'].= '"'. $val .'":"'.$arr[$key].(($key+1)==count($tm['param'])?'"':'",');
        }
        $params['ParamString'] .= '}';
        // 计算签名并把签名结果加入请求参数
        $params ['Signature'] = $this->computeSignature ( $params,$this->SecretKey );
        // 发送请求
        $url = 'https://sms.aliyuncs.com/?' . http_build_query ( $params );

        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $url );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_TIMEOUT, 10 );
        $result = curl_exec ( $ch );
        curl_close ( $ch );
        log_message('error','sms:'.json_encode($result));
        return $result = json_decode ( $result, true );
    }

    // 生成短信验证码
    function create_very_code($code, $mobile, RequestInterface $request){
        $db = new \App\Models\Admin\Sms();
        $db->save([
            'code'=>$code,
            'phone'=>$mobile,
            'ipaddr'=>$request->getIPAddress(),
            'ua'=>$_SERVER['HTTP_USER_AGENT']
        ]);
    }

    // 检查验证码是否使用或过期
    function ck_very_code($code = '', $moble = ''){
        $db = new \App\Models\Admin\Sms();

        if(!$data = $db->where(['code'=>$code,'phone'=>$moble,'status'=>0])->first())
            return array('code'=>false,'msg'=>'验证失败, 验证码已使用过!');

        $time = time();
        $sT = $data["createtime"];
        $lasttime = strtotime("$sT +15 minute");

        if( $time > $lasttime ){
            return array('code'=>false,'msg'=>'验证失败,验证码已过期!');
        }
        $db->save(['id'=>$data["id"],'status'=>1]);
        return array('code'=>true,'msg'=>'OK!');
    }
}
