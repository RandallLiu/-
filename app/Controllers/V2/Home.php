<?php
namespace App\Controllers\V2;

use App\Libraries\LibComp;

class Home extends BC
{
    function __construct() {
    }

    public function index() {
        return $this->display([
            'view_path' => '/index'
        ]);
    }


    public function colleges(){
        return $this->display([
            'view_path' => '/Colleges/index'
        ]);
    }

    // 设置省份区域
    public function province(){
        $province = $this->U('province');
        if ( !$province ) $province = 11;
        $this->session->set('province',$province);
        return $this->toJson('设置成功!');
    }

    public function te(){
        $kemu = kemu();

        return $this->toJson(["data"=>$kemu]);
    }
}