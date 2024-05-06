<?php
namespace App\Controllers\V2;

use App\Libraries\LibComm;
use App\Libraries\LibComp;
use App\Services\sms;

class Kemu extends BC
{
    function __construct() {
    }

    // 设置
    function selected(){
        if ( $this->request->getMethod() == 'post' ) {
            $P = $this->U();
            if ( $P['typeId'] == '3' && count($P['kemu']) != 3 ) return $this->setError("至少选3个学科!");
            if ( in_array($P['typeId'],['2073','2074']) && count($P['kemu']) != 2 ) return $this->setError("至少选2个学科!");

            if ( !$P['batch'] ) {
                $section = (new \App\Models\Resource\Sections())->where(['year' => $this->year(), 'province_id' => $P['province'], 'score' => $P['score']])->first();
                $P['batch'] = $section ? $section['batch'] : 10; $P['section'] = $section ? $section['total'] : 0;
            }

            // 如果登录 则保存信息
            if ( session("name") && session('id')) {
                (new \App\Models\Users())->save(['id' => session('id'),'province'=>$P['province'],'score'=>$P['score'],'kemu'=> json_encode($P) ]);
            }

            $P['type_name'] = LibComm::$kemu[$P['typeId']];
            $P['province_name'] = LibComm::$province[$P['province']];
            $this->session->set("kemu",$P);
            return $this->toJson('设置成功');
        }
        return $this->render(['view_path' => '/Comm/province']);
    }

    // 获取科目
    public function kemu(){
        return $this->toJson(['data'=>kemu()]);
    }

    // 获取科目
    public function get_kemu_item(){
        $p = $this->U('province');
        $data['kemu'] = kemu()[$p];
        //var_dump($data['kemu']);return ;
        $data['province'] = $p;
        return $this->render(['view_path' => '/Comm/kemu','data' => $data]);
    }

    // 一分一段
    public function get_sections_score(){
        $P = $this->U(); $db = (new \App\Models\Resource\Sections());
        $data = $db->select("total,rank_range,batch as batch_code,batch_name as batch")->where('year',$this->year())->search($P) ->first();
        log_message('error',$db->getLastQuery());
        return $this->toJson(['data'=>$data]);
    }
}