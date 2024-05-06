<?php
namespace App\Controllers\v1;

use App\Libraries\LibComp;
use App\Models\Resource\ProScore;
use App\Models\Resource\Schools;
use App\Models\Resource\SchProScore;
use App\Models\Resource\SchSpecial;
use App\Models\Resource\SchSpePlan;
use App\Models\Resource\SchSpeScore;
use App\Models\Resource\Special;
use CodeIgniter\Config\Services;
use CodeIgniter\Model;
use WpOrg\Requests\Requests;
use App\Controllers\Base;

class Colleges extends Base
{
    function __construct() {}

    // 大学列表
    public function school_data() {
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] ,$err);
        $P = $this->U();
        $P['searchField'] = 'name,short,code_enroll,phone,school_phone,type_name';
        $data = (new Schools())->search($P)->whereAuth()->asObject()->page( $this->_page() , $this->_size(), true );
        foreach ( $data['data'] as $row ) {
            $url = strrpos("://", $row->icon) !== false ? $row->icon : (file_exists("./$row->icon") ? (base_url().$row->icon):'');
            $row->icon = $url; unset($row->fenxiao);
        }
        return $this->toJson($data);
    }

    // 更新大学信息
    public function school_set_update() {
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] ,$err);
        // 判断是否POST提交
        if ( $this->request->getMethod() == 'post' ) {
            $P = $this->U();
            if ((new Schools())->save($P) ) return $this->toJson('保存成功!');
        }
        return $this->setError('保存失败');
    }

    // 学校详情
    public function school_detail(){
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] ,$err);
        if ($school_data = (new Schools())->where('id',$this->U('id'))->first()) {
            return $this->toJson(['data'=>$school_data]);
        }
        return $this->setError('获取失败');
    }

    // 学校下拉框选项或列表
    public function school_select_data() {
        if ( $err = $this->actionAuth() ) return $this->setError( $this->filed[$err] ,$err);
        $school_data = (new Schools())->select('id as value,name as label,icon')->findAll();
        return $this->toJson(['data'=>$school_data]);
    }

    // 删除学校
    public function school_delete() {
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] ,$err);
        if ( (new Schools())->where('id',$this->U('id'))->first() && (new Schools())->where('id',$this->U('id'))->delete() ) {
            return $this->toJson('删除成功');
        }
        return $this->setError('删除失败');
    }

    // 学校开设专业
    public function school_specials() {
        if ( $err = $this->actionAuth() ) return $this->setError( $this->filed[$err] ,$err);
        $P = $this->U();
        $school_data = (new Schools())->where('id',$P['schid'])->first();
        $P['searchField'] = 'code,special_name,type_name,level2_name,level3_name';

        $data = (new SchSpecial())->where('school_id',$school_data?$school_data['school_id']:0)
            ->search($P)->whereAuth()->asObject()->page( $this->_page() , $this->_size(), true );
        return $this->toJson($data);
    }

    // 获取学校详情条件搜索
    public function school_detail_search(){
        if ( $err = $this->actionAuth() ) return $this->setError( $this->filed[$err] ,$err);
        $P = $this->U(); $province_data_dict = LibComp::get_dict_data("PROVINCE");
        $school_data = (new Schools())->where('id',$P['id'])->first();
        $key = "";            $search_data = [];            $province_dict = []; $province_score_data = [];
        // 各省份
        if ( $P['sch_type'] == 'province_score' ) {
            // 所有省分数线记录
            $province_score_data = (new SchProScore())->where('school_id',$school_data['school_id'])->findAll();
            // 提取省份
            $province_data = (new SchProScore())->distinct()->select("province_id")->where('school_id',$school_data['school_id'])->findAll();
            foreach ( $province_data as $k=>$pv ) {
                $years = [];
                foreach ( $province_score_data as $row ) {
                    if ( $pv['province_id'] == $row['province_id'] ) $years[] = $row['year'];
                }
                $a = array_unique($years,SORT_NUMERIC);
                foreach ( $a as $v ) {
                    $search_data[$pv['province_id']][] = $v;
                }
                $province_dict[$pv['province_id']] = $province_data_dict[$pv['province_id']];
                if ( $k == 0) $key = $pv['province_id'];
            }
            $data['year'] = $search_data;
            $data['province'] = $province_dict;
        }

        if ( $P['sch_type'] == 'special_score' ) {
            // 专业分数线
            $province_score_data = (new SchSpeScore())->where('school_id',$school_data['school_id'])->findAll();
            // 提取省份
            $province_data = (new SchSpeScore())->distinct()->select("province")->where('school_id',$school_data['school_id'])->findAll();
            foreach ( $province_data as $k=>$pv ) {
                $years = [];
                foreach ( $province_score_data as $row ) {
                    if ( $pv['province'] == $row['province']) $years[] = $row['year'];
                }
                $a = array_unique($years,SORT_NUMERIC);
                foreach ( $a as $v ) {
                    $search_data[$pv['province']][] = $v;
                }
                $province_dict[$pv['province']] = $province_data_dict[$pv['province']];
                if ( $k == 0) $key = $pv['province'];
            }
        }

        if ( $P['sch_type'] == 'special_plan' ) {
            // 专业分数线
            $province_score_data = (new SchSpePlan())->where('school_id',$school_data['school_id'])->findAll();
            // 提取省份
            $province_data = (new SchSpePlan())->distinct()->select("province")->where('school_id',$school_data['school_id'])->findAll();
            foreach ( $province_data as $k=>$pv ) {
                $years = [];
                foreach ( $province_score_data as $row ) {
                    if ( $pv['province'] == $row['province']) $years[] = $row['year'];
                }
                $a = array_unique($years,SORT_NUMERIC);
                foreach ( $a as $v ) {
                    $search_data[$pv['province']][] = $v;
                }
                $province_dict[$pv['province']] = $province_data_dict[$pv['province']];
                if ( $k == 0) $key = $pv['province'];
            }
        }
        $data['year'] = $search_data;
        $data['province'] = $province_dict;
        $data['first'] = $key;
        $data['data'] = $province_score_data;
        return $this->toJson(['data' => $data]);
    }


    // 专业列表
    public function special_data() {
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] ,$err);
        $P = $this->U();
        $P['searchField'] = 'name,spcode';
        $data = (new Special())->search($P)->whereAuth()->asObject()->page( $this->_page() , $this->_size(), true );
        return $this->toJson($data);
    }

    // 保存专业信息
    public function special_set_update(){
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] ,$err);
        // 判断是否POST提交
        if ( $this->request->getMethod() == 'post' ) {
            $P = $this->U();
            if ((new Special())->save($P) ) return $this->toJson('保存成功!');
        }
        return $this->setError('保存失败');
    }

    // 专业详情
    public function special_detail(){
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] ,$err);
        if ($special_data = (new Special())->where('id',$this->U('id'))->first()) {
            $data['special'] = $special_data;
            $data['schools'] = (new Schools())->select("name,province_name,level_name,type_name,school_nature_name")->where("(select school_id from tzy_schools_specials where special_id='{$special_data["special_id"]}')")->findAll();
            return $this->toJson(['data'=>$special_data]);
        }

        return $this->setError('获取失败');
    }

    // 删除专业
    public function special_delete() {
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] ,$err);
        if ( (new Schools())->where('id',$this->U('id'))->first() && (new Schools())->where('id',$this->U('id'))->delete() ) {
            return $this->toJson('删除成功');
        }
        return $this->setError('删除失败');
    }

    // 院校开设
    public function special_shcools_data(){
        if ( $err = $this->actionAuth() ) return $this->setError( $this->filed[$err] ,$err);
        $P = $this->U();
        if ( !$P['special_id'] ) $this->toJson(['data'=>[],'total'=>0]);
        $P['searchField'] = 'name,short,code_enroll,phone,school_phone,type_name';
        $data = (new Schools())
            ->where("school_id in ( select school_id from tzy_schools_specials where special_id = '{$P['special_id']}' )")
            ->search($P)->whereAuth()->asObject()->page( $this->_page() , $this->_size(), true );

        // log_message('error',(new Schools())->getLastQuery());
        foreach ( $data['data'] as $row ) {
            $url = strrpos("://", $row->icon) !== false ? $row->icon : (file_exists("./$row->icon") ? (base_url().$row->icon):'');
            $row->icon = $url; unset($row->fenxiao);
        }

        return $this->toJson($data);

    }
}