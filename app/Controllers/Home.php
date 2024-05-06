<?php
namespace App\Controllers;

use App\Libraries\LibComp;
use WpOrg\Requests\Requests;

class Home extends Base
{
    function __construct(){
    }

    public function index(){
	    // return $this->toJson('欢迎使用admin后台管理');
        return redirect()->to('/');
	}

    // 登出
    public function logout(){
        $this->session->destroy();
        return redirect()->to('/');
    }

    // 图形验证码
    public function captcha(){
        \App\Libraries\LibComm::Captcha();
    }

    //
    public function layout(){
        return $this->display(['view_path'=>'main']);
    }

    public function sign(){

        return $this->toJson('登录成功');
    }

    // 初化图表数据
    public function init_charts_data() {
        $this->actionAuth();
        // 居民信息统计
        $residents_num_data = (new \App\Models\Archives\Residents())->select('count(0) as num')->where('status',0)->first();
        // 事件上报数
        $event_num_data = (new \App\Models\Archives\Events())->select('count(0) as num')->first();
        // 村务发布
        $pubcontents_num_data = (new \App\Models\Archives\AffPubcontents())->select('count(0) as num')->first();
        // 便民服务
        $affconven_num_data = (new \App\Models\Archives\AffServices())->select('count(0) as num')->first();
        // 问题反映
        $problem_num_data = (new \App\Models\Archives\Problems())->select('count(0) as num')->first();
        //
        $total = (($pubcontents_num_data ? intval($pubcontents_num_data['num']) :0) + ($affconven_num_data ? $affconven_num_data['num'] : 0) );
        //
        $number_data = [
            ['num' => $residents_num_data?intval($residents_num_data['num']):0,'title' => '居民信息统计' , 'icon' => 'icon-icon- iconfont' ,'color2' => '--next-color-warning-lighter','color3'=>'--el-color-warning'],
            ['num' => $event_num_data?intval($event_num_data['num']):0,'title' => '事件上报信息统计' , 'icon' => 'iconfont icon-gerenzhongxin' ,'color2' => '--next-color-danger-lighter','color3'=>'--el-color-danger'],
            ['num' => $total,'title' => '村务及便民服务' , 'icon' => 'fa fa-user-md' ,'color2' => '--next-color-success-lighter','color3'=>'--el-color-danger'],
            ['num' => $problem_num_data?intval($problem_num_data['num']):0,'title' => '问题反映信息' , 'icon' => 'fa fa-user-md' ,'color2' => '--next-color-success-lighter','color3'=>'--el-color-danger']
        ];
        $data['number_data'] = $number_data;

        $sdate = date('Y-m-d',strtotime('-30 day'));

        // 事件上报统计
        $event_data = (new \App\Models\Archives\Events())->select("count(0) as nums,date_format(createtime,'%m/%d') as dt")->where("createtime >= $sdate")->orderBy('dt','asc')->groupBy('dt')->findAll();
        // 村务发布统计
        $pubcontents_data = (new \App\Models\Archives\AffPubcontents())->select("count(0) as nums,date_format(createtime,'%m/%d') as dt")->where("createtime >= $sdate")->orderBy('dt','asc')->groupBy('dt')->findAll();
        // 便民服务
        $affconven_data = (new \App\Models\Archives\AffServices())->select("count(0) as nums,date_format(createtime,'%m/%d') as dt")->where("createtime >= $sdate")->orderBy('dt','asc')->groupBy('dt')->findAll();
        // 问题反映
        $problem_data = (new \App\Models\Archives\Problems())->select("count(0) as nums,date_format(createtime,'%m/%d') as dt")->where("createtime >= $sdate")->orderBy('dt','asc')->groupBy('dt')->findAll();
        //
        $month_data = []; $event_nums = []; $pubcontents_nums = [];$conven_nums = [];$problem_nums = []; $age_nums = [];

        for ($i = 30 ; $i >= 0 ; $i-- ) {
            $day = date('m/d',strtotime("- $i day"));
            $event_num = $pubcontents_num = $conven_num = $problem_num = 0;
            foreach ($event_data as $item) {
                if ( $day == $item['dt'] ) {$event_num = $item['nums'];}
            }

            foreach ($pubcontents_data as $item) {
                if ( $day == $item['dt'] ) {$pubcontents_num = $item['nums'];}
            }

            foreach ($affconven_data as $item) {
                if ( $day == $item['dt'] ) {$conven_num = $item['nums'];}
            }

            foreach ($problem_data as $item) {
                if ( $day == $item['dt'] ) {$problem_num = $item['nums'];}
            }

            $month_data[] = $day;
            $event_nums[] = intval($event_num);
            $pubcontents_nums[] = intval($pubcontents_num);
            $conven_nums[] = intval($conven_num);
            $problem_nums[] = intval($problem_num);
        }

        // 年龄分布
        $age_data = (new  \App\Models\Archives\Residents())
            ->select("
            case 
                when age < 14 then '0~13岁' 
                when age > 14 and age < 20 then '14~20岁' 
                when age > 20 and age < 30 then '21~30岁' 
                when age > 30 and age < 40 then '31~40岁' 
                when age > 40 and age < 50 then '41~50岁' 
                when age > 50 and age < 60 then '51~60岁' 
                when age > 60 and age < 70 then '61~70岁' 
                else '70岁以上' 
            end as age,
            count(0) as num
            ")
            ->where('status',0)
            ->orderBy('age',"asc")
            ->groupBy('age')
            ->findAll();
        foreach ( $this->_age_range() as $k=>$v) {
            $num = 0;
            foreach ( $age_data as $item ) {
                // log_message('error',"$k--$v---{$item['age']}");
                if ($v == $item['age']) $num = intval($item['num']);
            }
            $age_nums[] = $num;
        }
        // log_message('error',(new  \App\Models\Archives\Residents())->getLastQuery());
        $data['event_nums'] = $event_nums;
        $data['pubcontents_nums'] = $pubcontents_nums;
        $data['conven_nums'] = $conven_nums;
        $data['problem_nums'] = $problem_nums;
        $data['age_data'] = $age_data;
        $data['day_data'] = $month_data;

        $data['age_data'] = $age_nums;
        $data['age_range'] = $this->_age_range();

        return $this->toJson(['data'=> $data]);
    }

    protected function _age_range(){
        return [
            '0~13岁',
            '14~20岁',
            '21~30岁',
            '31~40岁',
            '41~50岁',
            '51~60岁',
            '61~70岁',
            '71以上'
        ];
    }

    public function set_dict(){
        // $dict_batch = array_flip(json_decode( file_get_contents(WRITEPATH."uploads/data/dict.batch.json"),true ));
        // return $this->toJson(['data'=>$dict_batch]);
        $dict_db = new \App\Models\Admin\Dictionary();
        $pro_score_db = new \App\Models\Resource\ProScore();
        // 获取字典父级类型
        $dict_data = $dict_db->where('code','PROVINCE')->asArray()->first();
        // 批次
        // $data = $pro_score_db->distinct()->select("batch, batch_name")->findAll();
        // 类别
        // $data = $pro_score_db->distinct()->select("type, type_name")->orderBy('type','asc')->findAll();
        // 招生类型
        // $data = (new \App\Models\Resource\SchSpePlan())->distinct()->select('')

        // 专业分类
        // $data = (new \App\Models\Resource\Special())->distinct()->select('level3 as code,level3_name as name')->findAll();

        // 院校类型
        // $data = (new \App\Models\Resource\Schools())->distinct()->select('type as code,type_name as name')->findAll();

        // 办学类型
        $data = (new \App\Models\Resource\Schools())->distinct()->select('type as code,type_name as name')->findAll();

        // 省份
        // $data = json_decode(file_get_contents(WRITEPATH."uploads/data/province.json"),true);
            /*
                foreach ( $data as $k=>$v ) {
                    $dict_db->save([
                        'id' => 0,
                        'name' => $v,
                        'code' => $k,
                        'parentid' => $dicdata['id'],
                    ]);
             }*/

        foreach ( $data as $row ) {
            $dict_db->save([
                'id' => 0,
                'name' => $row['name'],
                'code' => $row['code'],
                'parentid' => $dict_data['id'],
            ]);
        }

    }

    function heartbeat(){
        return session('id') ? $this->toJson("success") : $this->setError("error");
    }

    public function test(){
        echo intval(9/5);
    }

    public function login(){
        $URL = $this->U('url');
        if ( session('id') ) return redirect()->to($URL?$URL:"/");
        return view('/_login2');
    }
}
