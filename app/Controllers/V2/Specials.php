<?php
namespace App\Controllers\V2;

class Specials extends BC
{
    protected $db;
    function __construct() {
        $this->db = new \App\Models\Resource\Special();
    }

    // 专业列表
    public function data() {
        $P = $this->U();
        $P['searchField'] = 'name,spcode,level2_name,level3_name';
        if ( $this->s_name() &&  $this->s_dir()) $this->db->orderBy($this->s_name(),$this->s_dir());
        $data = $this->db->search($P)->whereAuth()->asObject()->pagination( $this->page() , $this->size());
        return $this->toJson($data);
    }

    // 专业列表
    public function index() {
        return $this->display([
            'view_path' => '/Specials/index'
        ]);
    }

    // 专业开设学校
    public function schools(){
        $data = $this->U();
        return $this->render([
            'view_path' => '/Specials/schools',
            'data' => $data
        ]);
    }

    // 选专业
    public function choose(){
        $data["year"] = $this->year();
        return $this->display([
            'view_path' => '/Specials/choose',
            'data' => $data
        ]);
    }
}