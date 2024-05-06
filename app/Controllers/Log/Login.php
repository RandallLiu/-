<?php
namespace App\Controllers\Log;
use App\Controllers\Base;

class Login extends Base {
    protected $db;

    function __construct() {
        $this->db = new \App\Models\Admin\LoginLogs();
    }

    // 数据列表
    public function data() {
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] ,$err);
        $P = $this->U();
        $P['searchField'] = 'username';
        $data = $this->db->search($P)->orderBy('createtime','desc')
            ->page( $this->_page() , $this->_size() );

        return $this->toJson($data);
    }
}