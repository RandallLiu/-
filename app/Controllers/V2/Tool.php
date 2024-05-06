<?php
namespace App\Controllers\V2;

class Tool extends BC
{
    function __construct() {
    }

    // 省控线
    public function score(){
        $data['years'] = [$this->year(),$this->year()-1,$this->year() - 2];
        $data['current_year'] = $this->year();

        return $this->display([
            'view_path' => '/Tool/score',
            'data' => $data
        ]);
    }

    // 省控线数据列表
    public function score_data(){
        $P = $this->U(); $db = (new \App\Models\Resource\ProScore());
        $data = $db->select('province,name , type_name as tname,batch_name as batch,score_section as section,score,year')
            ->search($P)->pagination( $this->page() , $this->size());
        return $this->toJson($data);
    }

    // 一分一段
    public function section(){
        $data['years'] = [$this->year(),$this->year()-1,$this->year() - 2];
        $data['current_year'] = $this->year(); $kemu = session('kemu');
        $km_type = json_decode(file_get_contents(WRITEPATH."uploads/data/2023.kemu.dict.json"),true);

        $province = $kemu['province']??'11';
        // 考生类型
        $data['kslx'] = $km_type[$province];

        return $this->display([
            'view_path' => '/Tool/sections',
            'data' => $data
        ]);
    }

    // 一分一段数据
    public function section_data(){
        $P = $this->U(); $db = (new \App\Models\Resource\Sections());
        $data = $db->select('score,year,num,total')
            ->search($P)->orderBy('score','desc')->pagination( $this->page() , $this->size());
        return $this->toJson($data);
    }
}