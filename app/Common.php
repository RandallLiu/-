<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the frameworks
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @link: https://codeigniter4.github.io/CodeIgniter4/
 */


use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\HTTP\RequestInterface;
use Config\Services;

if (! function_exists('display')) {
    /**
     * 输出视图
     * @param string $view 视图名称
     * @param array $array 数据项
     * @param array $options
     */
    function display($data = [],$options = []) {
        $view = array_key_exists('view_path',$data) ? $data['view_path'] :'';
        $layout = (isset($data['layout']) && $data['layout'] === 'Html');
        if(mb_strpos($view , '/')===false && $view!= 'main' ){
            $routes = Services::router();
            $arr = explode('\\',$routes->controllerName());
            $view = sprintf('/%s/%s/%s',$arr[3],$arr[4],$view?:$routes->methodName());
        }
        unset($data['layout'],$data['view_path']);
        return view(
            ($layout) ? $view :'/Layout/load_template',
            [
                'data' => isset($data['data'])?$data['data']:$data,
                'view' => $view,
                'options' => $options
            ]
        );
    }
}


if(! function_exists('Login')){
    /**
     * 登录
     * @param string $account 用户名
     * @param string $password 密码
     * @return array
     * */
    function Login( $A,$sPass,RequestInterface $request){
        $db = new App\Models\Admin\Users();
        $P = md5(decrypt($sPass));
        if(!$P) exit ('密码错误!');
        $RS = \App\Libraries\LibComp::U($A,$P);
        if(( !$RS || !array_key_exists('code',$RS) ) ) {
            if ($resp_data = $db->select('id,username,nickname,post,power,companyid,customerid,type')->where( $RS )->asArray()->first() ) {
                $session = \CodeIgniter\Config\Services::session();$log = new \App\Models\Admin\LoginLogs();$comm = new \App\Services\comm();
                $ip = $request->getIPAddress();
                // 登录日志
                $log->save(['userId' => $resp_data["id"], 'username' => $resp_data['username'], 'ip' => $request->getIPAddress(), 'ua' => $request->getUserAgent()]);
                // 更新用户最后登录情况
                $db->set('last_ip', $ip)->set('login_lasttime', date('Y-m-d H:i:s'))->where('id', $resp_data['id'])->update();
                // 保存 session
                $session->set(['id' => $resp_data['id'], 'username' => $resp_data['username'], 'name' => ($resp_data['nickname'] ?: $resp_data['username']), 'post' => $resp_data['post'], 'power' => $resp_data['power'], 'company' => $resp_data['companyid'], 'custId' => $resp_data['customerid'] ?: 0]);
                // 保存用户角色
                $session->set('Roles', $comm->get_roles_data());
                //
                $RS = ['code' => true, 'msg' => '登录成功!'];
            }
        }
        return ($RS);
    }
}

if (! function_exists('decrypt')){
    /**
     * 解密
     * @param string $str 解密字符串
     * @return string;
     * */
    function decrypt($str){
        $private_key_file_path = WRITEPATH . 'cert/fbd380a85db64e0d45ce0a71fcec07a5.pem';
        $private_key = file_get_contents($private_key_file_path);
        $is_decrypt = openssl_private_decrypt(base64_decode($str),$decrypted,$private_key);

        if($is_decrypt)
            return $decrypted;

        return $is_decrypt;
    }
}

// 检查权限
function ck_action($uri){
    $comm = new \App\Services\comm();
    return $uri ? $comm->check_auth($uri)   : false;
}

// 检查权限
function check_action($userId = 0,string $uri){
    $db = new \App\Models\Admin\Actions();
    $data = $db->select('id')->where("id in (select operation_id from admin_power where role_id in(select role_id from admin_users_role where user_id='$userId'))")
        ->where('uri',$uri)
        ->first();
    return ( ($userId == 0) || $data) ? true : false;
}


/**
 * 根据用户权限
 * */
function where_auth(){
    $data = (new \App\Services\Auth())->ckAuthoriztion();
    $Ids = []; $access_data = ($data['code'] === true) ? json_decode($data['data']->data->access_data,true) : "";
    if ( $access_data && is_array($access_data) ) $Ids = array_merge($access_data,[0]);
    return $Ids;
}

// 判断用户角色职位
function ckAuth( $auth = 'customer'){
    if ( $auth !== false && $auth == 'customer' ) return session('power') === $auth;

    if ( $auth === false ) {
        return hasRole(['admin','finance','operator','invoicer','sa','agent']);
    }
    return hasRole( $auth );
}

// 是否包含角色
function hasRole( $code ,$userId = 0 ){
    $db = new \App\Models\Admin\Roles();
    $self = session('id') == $userId;
    if ( in_array(session('power'),['all','admin','sa']) ) return true;
    is_array( $code ) ? $db->whereIn('a.code',$code ) : $db->where('a.code',$code );
    $data = $db->from('admin_roles as a', true)->join('admin_users_role as b', 'a.id=b.role_id', 'left')
        ->where(['b.user_id' => $userId ?: session('id')])->first();
    return ( $data ) ? true : false;
}


function create_guid($namespace = '') {
    $guid = '';
    $uid = uniqid("", true);
    $data = $namespace;
    $data .= $_SERVER['REQUEST_TIME'];
    $data .= $_SERVER['HTTP_USER_AGENT'];
    $data .= $_SERVER['REMOTE_ADDR'];
    $data .= $_SERVER['REMOTE_PORT'];
    $hash = strtoupper(hash('ripemd128', $uid . $guid . md5($data)));
    $guid = '' .
        substr($hash,  0,  8) .
        substr($hash,  8,  4) .
        substr($hash, 12,  4) .
        substr($hash, 16,  4) .
        substr($hash, 20, 12) .
        '';
    return strtolower($guid);
}
// 下载
function force_download($filename = '', $data = '', $set_mime = FALSE) {
    if ($filename === '' OR $data === '') return;

    elseif ($data === NULL) {
        if ( ! @is_file($filename) OR ($filesize = @filesize($filename)) === FALSE) {
            return;
        }
        $filepath = $filename;
        $filename = explode('/', str_replace(DIRECTORY_SEPARATOR, '/', $filename));
        $filename = end($filename);
    }
    else {
        $filesize = strlen($data);
    }

    $mime = 'application/octet-stream';

    $x = explode('.', $filename);
    $extension = end($x);

    if ($set_mime === TRUE)
    {
        if (count($x) === 1 OR $extension === '') {
            return;
        }

        if (isset(\Config\Mimes::$mimes[$extension])) {
            $mime = is_array(\Config\Mimes::$mimes[$extension]) ? \Config\Mimes::$mimes[$extension][0] : \Config\Mimes::$mimes[$extension];
        }
    }

    if (count($x) !== 1 && isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/Android\s(1|2\.[01])/', $_SERVER['HTTP_USER_AGENT'])) {
        $x[count($x) - 1] = strtoupper($extension);
        $filename = implode('.', $x);
    }

    if ($data === NULL && ($fp = @fopen($filepath, 'rb')) === FALSE)
    {
        return;
    }

    if (ob_get_level() !== 0 && @ob_end_clean() === FALSE)
    {
        @ob_clean();
    }

    // Generate the server headers
    header('Content-Type: '.$mime);
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Expires: 0');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: '.$filesize);
    header('Cache-Control: private, no-transform, no-store, must-revalidate');

    if ($data !== NULL) {
        exit($data);
    }

    while ( ! feof($fp) && ($data = fread($fp, 1048576)) !== FALSE) {
        echo $data;
    }

    fclose($fp);
    exit;
}

// 隐藏中间手机号
function mobile_hide( $str ) {
    if (!ck_mobile ( $str ) ) return '';
    return preg_replace('/(\d{3})\d{4}(\d{4})/', '$1****$2',$str);
}

// 检测手机号是否
function ck_mobile( $str ) {
    return preg_match("/^1[345789]\d{9}$/", $str );
}

// 检测邮箱是否正确
function ck_email( $email ){
    return preg_match( "/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@([a-zA-Z0-9]+[-.])+([a-z]{2,5})$/ims" ,$email ) ;
}

if(! function_exists('get_menu_action')){
    function get_menu_action($menuid,$userId = 0,$isMenuRight = true){
        $model = new  \App\Models\Admin\Menu();
        if($isMenuRight) {
            return $model->select('id as rightId,menuid as muduleId,name,uri')->from('admin_operation', true)->where(['menuid' => $menuid, 'state' => 0])->asObject()->findAll();
        }
        else {
            return $model->get_menu_user_rights($userId, $menuid);
        }
    }
}

/**
 * 获取树形结构
 * */
function menu_tree($data,$userId = 0,$right = false){
    $db = new  \App\Models\Admin\Menu();
    foreach ( $data as $row ) {
        $subData = $db->select('id,title as label,parentId')->asObject()->where('status','0')->where('parentId',$row->id)->findAll();

        $child = menu_tree($subData,$userId);

        $rights = get_menu_action($row->id,$userId,($userId == 0 ? true : false));

        if( $child ) {
            $row->children = $child;
            $row->disabled = true;
        }
        if( $rights ) {
            $row->isPenultimate = true;
            $row->disabled = true;
            $auth = [];
            foreach ( $rights as $item ) {
                $auth[] = ['id'=>strval($row->id) . '.'.strval($item->rightId),'label'=>$item->name];
            }
            $row->children = $auth;
        }
        if($row->parentId == 0) {$row->disabled = true;}

        unset($row->parentId);unset($row->uri);
    }
    return $data;
}

function menu_power_build($userId , $hidden = [0]){
    // 登录时获取菜单
    $db = new \App\Models\Admin\Menu();
    $db->select('id,title as label,parentid,sort')->whereIn('status',$hidden);
    if ( $userId ) {
        $data = $db->where(
            "id in (select distinct menu_id from admin_power where role_id in (select distinct role_id from admin_users_role where user_id=$userId))")
            ->orderBy('sort', 'asc')->asArray()->findAll();
    } else {
        $data = $db->distinct()
            //->where('parentid > 0')
            ->orderBy('sort', 'asc')->asArray()->findAll();
    }

    function find_tree($data,$level = 0){
        $db = new \App\Models\Admin\Menu();
        $up_data = $up_fd =[];
        // 判断父级结点不为root时
        if( $data && $data['parentid'] > 0 ){
            $fd = $db->select('id,title as label,parentid,sort')->where('id',$data['parentid'])->asArray()->first();
            // 查找父级
            $up_fd = find_tree($fd,($level + 1));
        }
        $up_data[] = $data;
        return  (array_merge($up_data,$up_fd));
    }

    $find_data = [];
    foreach ( $data as $row ) {
        $find_data[] = find_tree($row,1);
    }
    $root = [];$T = []; $ALL = [];


    foreach ( $find_data as $item ) {
        $a = array_filter($item,function ($r) {
            return $r['parentid'] == 0 ? $r : [];
        });
        foreach ($a as $v) {
            if (!in_array($v['label'], $T)) {
                $T[] = $v['label'];
                $root[] = ($v);
            }
        }

        $b = array_filter($item,function ($r) {
            return $r;
        });
        foreach ($b as $k=>$v) {
            $ALL[] = $v;
        }
    }

    function bm( $data, $all , $userId){
        $child = $tree_data = [];

        $tree_data["label"] = $data["label"];
        $tree_data["id"] = $data["id"];

        $rights = get_menu_action($data["id"], $userId ,( $userId == 0 ? true : false));

        if ( $rights ) {
            foreach ( $rights as $right ){
                $items[] = ['id'=>$right->muduleId.'.'.$right->rightId,'label'=>$right->name];
            }
            if ($items) {$tree_data["children"] = $items;$tree_data['isPenultimate'] = true;}
        }
        if($data['parentId'] == 0) {$tree_data['disabled'] = true;}
        foreach ( $all as $row ) {
            if( $data['id'] == $row['parentid'] ) {
                $child[] = bm( $row , $all , $userId );
            }
        }
        if ($child) {$tree_data['children'] = $child;$tree_data['disabled'] = true;}
        return $tree_data;
    }
    foreach ( $root as $item ){
        $tree[] = bm($item,$ALL,$userId);
    }
    return ($tree);
}

function toXml( $xml,$data, $eIsArray=FALSE){
    try {
        $version = '1.0';
        $encoding = 'UTF-8';
        $root = 'xml';
        if (!$eIsArray) {
            $xml->openMemory();
            $xml->startDocument($version, $encoding);
            $xml->startElement($root);
        }

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $xml->startElement($key);
                toXml($xml, $value, TRUE);
                $xml->endElement();
                continue;
            }
            $xml->writeElement($key, $value);

        }
        if (!$eIsArray) {
            $xml->endElement();
            return $xml->outputMemory(true);
        }
    } catch (\Exception $ex) {
        log_message('error','Exception:'.json_encode( $ex->getTrace() ));
    }
}

//编译请求头格式和数据流 multipart/form-data
function buildData($param,$delimiter){
    $data = '';
    $eol = "\r\n";
    foreach ($param as $name => $content) {
        $data .= "--" . $delimiter . "\r\n". 'Content-Disposition: form-data; name="' . $name . "\"\r\n\r\n". $content . "\r\n";
    }
    $data .= "--" . $delimiter . $eol. 'Content-Disposition: form-data; name="media"; "' . "\r\n". 'Content-Type:application/octet-stream'."\r\n\r\n";
    $data .= "--" . $delimiter . "--\r\n";
    return $data;
}

// 判断身份证号是否正确
function ck_idnum($id){
    $pattern = "/^[1-9]\d{5}(18|19|20)\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/";
    if (preg_match($pattern,$id)) {
        return true;
    }
    return false;
}

// 计算年龄
function get_age($id){
    // 身份证号是否正确
    if (!ck_idnum($id)) return 0;
    #从身份证中获取出生日期 截取日期并转为时间戳
    $birth_Date = strtotime(substr($id, 6, 8));//
    #格式化[出生日期]
    $Year = date('Y', $birth_Date);//yyyy
    $Month = date('m', $birth_Date);//mm
    $Day = date('d', $birth_Date);//dd

    #格式化[当前日期]
    $current_Y = date('Y');//yyyy
    $current_M = date('m');//mm
    $current_D = date('d');//dd
    #计算年龄 今年减去生日年
    $age = $current_Y - $Year;
    if($Month > $current_M || $Month == $current_M && $Day > $current_D){
        $age--;
    }
    return $age;
}

// 科目
function kemu(){
    /*
    $provinces = json_decode( file_get_contents(WRITEPATH."uploads/data/kemu.dict.json"),true );
    $kemu = [];$bc = new \App\Controllers\V2\BC();
    foreach ( $provinces as $p=>$item ) {
        foreach ( $item as $k=>$v ) {
            if ( $k == $bc->year() ) {
                $km = [];
                foreach ( $v as $key=>$value ) {
                    if (isset($value['id'])&&($key == 0 || $key == 1)) {
                        $km[] = [$value['id']=>$value['name']];
                    }
                }
                $kemu[$p] = $km;
            }
        }
    }*/
    $kemu = json_decode( file_get_contents(WRITEPATH."uploads/data/province.km.json"),true );
    return $kemu;
}

// 收藏
function favorite($Id,$type = "school_special") {
    $resp = false;
    // 判断是否存在记录
    if ( session('id') &&(new \App\Models\Users())->where("id",session('id'))->first()) {
        $db = new \App\Models\Resource\Favorite();
        // 判断是否已收藏
        if ($db->where(["relate_id"=>$Id,'relate_type'=> $type])->first()) {
            // 删除收藏
            $resp = $db->where(['relate_id' => $Id,'user_id' => session('id'),'relate_type' => $type])->delete();
        } else {
            // 保存收藏
            $resp = $db->save(['relate_id' => $Id,'user_id' => session('id'),'relate_type' => $type]);
        }
        // log_message('error',$db->getLastQuery());
    }
    return $resp;
}

function odds_data($P , $year){
    $db = new \App\Models\Resource\Odds();
    $kemu = session('kemu');$score = $kemu['score']; $batch = $kemu['batch'];
    $shool_key_position = [] ; $Avalue = 0;
    // 本科批选择 专业批次
    if ( $P['level'] == '2002' && $batch != '10') {
        /*  临界情况 */
        $section_db = (new \App\Models\Resource\Sections());
        // 位次条件
        $section_where = ['year' => $year, 'province_id' => $kemu['province'], 'type' => $kemu['typeId']];
        // 获取专科最大分数值
        $score_batch_section = $section_db->selectMax("score")->where("batch",10)->where($section_where)->first();
        // 获取分值所对应位置
        $temp_section = $section_db->where($section_where)->where('score', ($score_batch_section['score']))->first();
        //
        $batch = $temp_section['batch']; $score = $score_batch_section['score'];
    }
    // 条件
    $odds_argc = ['province'=>$kemu['province'],'batch' => ($batch) , 'type' => $kemu['typeId'],'year' => $year + 1];
    // 获取
    $odds_avalue = $db->where($odds_argc)->where("Avalue >= ($score - proscore)")->orderBy("Avalue","asc")->first();
    // 查询录取概率信息
    $load_odds_data = $db->where($odds_argc)->where("Avalue >= (($score - proscore) * 0.1)")->orderBy("Avalue","desc")->findAll();

    // 所有概率
    if ( $load_odds_data ) foreach ($load_odds_data as $k => $odds) {
        $shool_ids[] = $odds['school_id']; $shool_key_position["{$odds["school_id"]}"] = ["idx" => $k + 1 , 'value' => $odds['Avalue']];
        if ( $odds['school_id'] == $odds_avalue['school_id'] ) {
            $Avalue = $odds['Avalue'];
        }
    }

    return ['batch' => $batch,'score' => $score,'Avalue' => $Avalue, 'shool_key_position'=>$shool_key_position];
}