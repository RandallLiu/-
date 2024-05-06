<?php
namespace App\Controllers;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 *
 * @package CodeIgniter
 */

use App\Services\comm;
use CodeIgniter\Controller;
use Config\Services;

class Base extends Controller {
	/**
	 * An array of helpers to be loaded automatically upon
	 * class instantiation. These helpers will be available
	 * to all other controllers that extend BaseController.
	 *
	 * @var array
	 */
	protected $helpers = ['html','Form'];

    protected $secretKey = "";
    // 七牛帐户
    protected $AK = '';
    protected $SK = '';
    protected $liveAppId = '';

    // 是否验证登录
    protected $NLogin = false;
    // 是否验证VIP
    protected $VIP = false;

    protected $session;
    protected $cookie;
    protected $cache;
	/**
	 * Constructor.
	 */
	public function initController(\CodeIgniter\HTTP\RequestInterface $request, \CodeIgniter\HTTP\ResponseInterface $response, \Psr\Log\LoggerInterface $logger)
	{
		// Do Not Edit This Line
		parent::initController($request, $response, $logger);
        // Ajax 跨域请求
        $this->response->setHeader('Access-Control-Allow-Origin','*');
        $this->response->setHeader('Access-Control-Allow-Methods','GET,POST,PUT,DELETE');
        //
        $this->response->setHeader('Access-Control-Allow-Headers','Origin,token, X-Requested-With, Content-Type, Accept,Authorization');
		//--------------------------------------------------------------------
		// Preload any models, libraries, etc, here.
		//--------------------------------------------------------------------
		// E.g.:
        // $this->session = \Config\Services::session();
        // load env config
        $this->secretKey = env("conf.jwt.secretKey");
        $this->AK = env("conf.qiniu.AK");
        $this->SK = env("conf.qiniu.SK");
        $this->liveAppId = env("conf.live.appId");

        $this->session = \Config\Services::session();
        $this->cache = \Config\Services::cache();

        $this->check_login_vip();
	}

	private function _set_header(){
        $this->response->removeHeader('Set-Cookie');
    }

    // 加载视图
    function display( $data = [] , $options = []){
	    // 检测是否登录或VIP
        $this->check_login_vip();

        return display($data, $options );
    }

    function render( $data = [] ){
        $data['layout'] = 'Html';
        return display($data);
    }
    // JSON 输出
    function toJson($data = array(),$head = [],$status = true,$message = 'Success'){
        $this->_set_header();
        if(count($head)>0) {
            foreach($head as $k=>$v) {
                $this->response->setHeader($k,$v);
            }
        }
        if (is_array($data)) {
            if (!array_key_exists('msg', $data)) {
                $data['msg'] = $message;
            }
            if (!array_key_exists('code', $data)) {
                $data['code'] = $status;
            }
        } else{
            $data = ['code'=>$status,'msg'=>$data];
        }
        $data['status'] = 0;
        // 销毁
        if ( array_key_exists('code', $data) && ( $data['code'] || $status && $this->request->getMethod() == 'post' ) ) {
            // form_destroy_guid( $this->U('guid') );
        }
        $outStr=preg_replace('/":null/', '":""', $this->jsonGet($data));

        return $this->response->setJSON($outStr);
    }

    // 检测是否登录或VIP
    protected function check_login_vip($NL = false , $VIP = false){
        $this->NLogin = $NL?:$this->NLogin; $this->VIP = $VIP?:$this->VIP;
        // 判断是否登录
        if ( ($this->NLogin || $this->VIP) && !session("id")) {
            if ( !$this->request->isAJAX() ) {
                $url = base64_encode($_SERVER['REQUEST_URI']);
                header("Location:/login?url=$url"); exit();
            } else {
                $this->response_header(false,401);
            }
        }

        // 检测是否VIP
        if ( ($this->VIP && !$this->NLogin) && session("id") ) {
            if ( !$this->request->isAJAX() ) {
                $url = base64_encode($_SERVER['REQUEST_URI']);
                header("Location:/login?url=$url"); exit();
            } else {
                $this->response_header(false,410);
            }
        }

        if (($NL || $VIP) && !session('id')) {
            if ( !$this->request->isAJAX() ) {
                $url = base64_encode($_SERVER['REQUEST_URI']);
                header("Location:/login?url=$url"); exit();
            }
            $this->response_header(false, 401);
        }
    }

    protected function jsonGet($result) {
        return json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function setError( $msg = '' , $code = false ){
        $this->_set_header();
        if(is_object($msg) || is_array($msg)) {
            $message = [];
            foreach ( $msg as $k=>$m ) {
                $message[] = $m;
            }
            $msg = join('  ',$message);
        }
        $data = ['code'=> $code ,'msg'=>$msg?:'请求失败','errno'=>1];
        $outStr = preg_replace('/":null/', '":""', $this->jsonGet($data));
        return $this->response->setJSON($outStr);
    }

    protected function request($index = null,$filter = null){
        $post_data = $this->request->getPost($index,$filter);

        if ( !is_array($post_data) && !is_object($post_data) && $post_data)
            return $post_data;
        $get_data = $this->request->getGet($index,$filter);

        if ( !is_array($get_data) && !is_object($get_data) && $get_data)
            return $get_data;

        if ( $post_data && !$get_data ) return $post_data;

        if ( !$post_data && $get_data ) return $get_data;

        if ( $post_data && $get_data ) return array_merge($post_data,$get_data);

        return [];
	}

    protected function U($index = null,$filter = null){
        $resp_data = $this->request($index,$filter);
	    return $resp_data;
    }

    function actionAuth($chkpromise = false, $action_path = '') {
	    return $this->signature( $chkpromise , $action_path );
	}

    // 判断验签及权限
    protected function signature( $chkpromise = false , $action_path = '' ){
	    if ( $this->request->getMethod() == 'options') return 999999;
        $code = 0 ; $URI = (new \CodeIgniter\HTTP\URI(current_url(true)))->getPath();
        $data = $this->ckAuthoriztion();
        // 判断权限
        if( is_bool( $chkpromise ) && $data['code'] === true ) {
            $routes = Services::router();
            $ctln = $routes->controllerName();
            if( $chkpromise && $data["data"]->data->id )  {
                $ctl = explode('\\',$ctln);
                if ( !$action_path ) $action_path = sprintf('%s/%s/%s',$ctl[3],$ctl[4],$routes->methodName());
                $actionKey = $action_path;
                if( check_action($data["data"]->data->id ,$actionKey) ) return 100001;
            }
        }
        $aKey = (empty($action_path)||!$action_path) ? (($URI == '/' || empty($URI)) ? 'main' : substr($URI,1)) : $action_path;

        $this->operation_log(['type'=>'', 'userid'=> $data["data"]->data->id ,'username'=> $data["data"]->data->username ,'controller'=>$aKey,'ip' => $this->request->getIPAddress(),'action'=>$aKey,'uri'=>json_encode($this->U())]);
        if( $data['code'] !== true ) $code = 4001;
        return $code;
    }

    private function operation_log($data){
        $db = new \App\Models\Admin\OperationsLog();
        return $db->save($data);
    }

    // 返回token和refresh_toke
    protected function Authorizations($data = [],$is_fresh = false ){
        $auth = new \App\Services\Auth();
        return $auth->Authorizations($data,$is_fresh);
    }

    // 检测AccessToken是否合法
    protected function ckAuthoriztion(){
        $auth = new \App\Services\Auth();
        return $auth->ckAuthoriztion();
    }

    protected function userId () {
        $data = $this->ckAuthoriztion();
        return ($data['code'] === true) ? $data['data']->data->id : 0;
    }

    protected function user_name () {
        $data = $this->ckAuthoriztion();
        return ($data['code'] === true) ? $data['data']->data->username : "";
    }

    protected function nickname () {
        $data = $this->ckAuthoriztion();
        return ($data['code'] === true) ? $data['data']->data->nickname : "";
    }

    protected function auth_access() {
        $data = $this->ckAuthoriztion();
        return ($data['code'] === true) ? $data['data']->data->access_data : "";
    }

    // 分頁码
    protected function _page(){
        $page = $this->request('currentPage');
        //$size = $this->request('size');
        return $page?:1;
    }
    // 分页大小
    protected function _size(){
        $size = $this->request('pageSize');
        return $size?:10;
    }
    // 排序字段
    protected function _s_name(){
        $filed = $this->request('sort');
        return $filed;
    }
    // 排序方向
    protected function _s_dir(){
        $s_dir = $this->request('dir');
        return $s_dir;
    }

    // 输出表头信息
    private function response_header($message , $status = 200) {
        http_response_code($status);header('Content-Type: application/json');echo json_encode($message,JSON_UNESCAPED_UNICODE);exit();
    }

    // 邮件发送
    protected function sent_mail($options = []){
        $resp = false;
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();                                            // Send using SMTP
            $mail->Host       = '';                   // Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
            $mail->Username   = '';                  // SMTP username
            $mail->Password   = '';                      // SMTP password
            $mail->SMTPSecure = 'ssl';                                  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 465;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
            $mail->CharSet='UTF-8';
            //Recipients
            $mail->setFrom('', '');
            $mail->addAddress( $options["to"]);     // Add a recipient
            // Content
            $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $options["subject"];
            $mail->Body    = $options["body"];
            $resp = $mail->send();
        }
        catch ( Exception $ex ){
            log_message('error',"sent_mail:$mail->ErrorInfo");
        }
        return $resp;
    }

    protected $filed = [
        10001 => '验签失败,请检查参数是否正确!',
        4001=>'AccessToken Exp',
        10004=>'refreshToken Error!',
        10003=>'参数错误!',
        11001=>'访问失败,未授权',
        99999=>''
    ];
}
