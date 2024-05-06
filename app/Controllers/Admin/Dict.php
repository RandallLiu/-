<?php
namespace App\Controllers\Admin;

use App\Libraries\LibComp;
use App\Models\Admin;
use App\Controllers\Base;
use CodeIgniter\HTTP\Request;

class Dict extends Base {
    private $db;
    function __construct(){
        $this->db = new \App\Models\Admin\Dictionary();
    }

    // 数据列表
    public function data_list(){
        $this->actionAuth(true);
        $P = $this->U();
        $P['searchField'] = 'code,name';
        if( !$P['parentid'] ) $P['parentid'] = 0;
        $data = $this->db->select('id,name as label,code as key,parentid,status,css,remark')->search($P)->orderBy('sorting','asc')->paginates($this->_page());
        $data['data'] = $this->build_tree($data['data']);
        return $this->toJson($data);
    }

    // 保存
    public function save(){
        $this->actionAuth(true);
        $entity = new \App\Entities\Forms($this->U());
        if ($this->request->getMethod() == 'post') {
            $this->db->setValidationMessages($this->db->validationMessages);
            if ($this->db->save($entity)) {
                /*
                if( intval( $entity->parentid > 0 ) ) {
                    $PD = $this->db->where('id',$entity->parentid)->first();
                    $path = WRITEPATH. 'uploads/js';
                    $bl = '__'.$PD->code;

                    if(!file_exists($path)) {
                        mkdir($path,0777,true);
                    }

                    $data = $this->db->where('parentid',$entity->parentid)->findAll();
                    $code="var $bl={}\r\n";
                    foreach($data as $item){
                        $code .= $bl."['{$item->code}']={'name':'{$item->name}','hidden':{$item->hidden}";
                        if(strlen($item->remark)>0) {
                            $code .= ",'mark':'{$item->remark}'";
                        }
                        if(strlen($item->css)>0) {
                            $code .= ",'css':'{$item->css}'";
                        }
                        $code=$code.'};'."\r\n";
                    }
                    file_put_contents("$path/$bl.js",$code);
                }*/
                return $this->toJson('保存成功');
            }
        }
        return $this->setError($this->db->errors());
    }

    public function detail(){
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] );
        $Id = $this->U('id');
        if ( $Id && !$dict_data = $this->db->select('id,name,code,parentid,sorting,remark')->where('id',$Id )->first()) {
            return $this->setError('参数错误');
        }
        $data['form_item'] = $this->_form($Id);
        $data['data'] = $dict_data ?? [];
        return $this->toJson($data);
    }

    // 删除
    public function delete(){
        $this->actionAuth(true);
        if( $this->db->where('id',$this->U('id'))->delete() )
            return $this->toJson('已删除');
        return $this->setError('删除失败');
    }

    // 启用/禁用
    public function disenabled(){
        $this->actionAuth(true);
        if ( $data = $this->db->where('id',$this->U('id'))->first()) {
            if( $this->db->where('id',$data->id)->set('status',!$data->status)->update() ) {
                $this->db->where('parentid',$data->id)->set('status',!$data->status)->update();
                return $this->toJson("已成功设置");
            }
        }
        return $this->setError("设置失败!");

    }

    // 左侧树状数据
    public function data_tree( $t = 0 ){
        $this->actionAuth();
        $db = $this->db;
        $select = 'id,name as label,code as key,status,remark';
        if ($t) $select = 'id,id as value,name as label,code as key,status,remark';
        $data = $db->select($select)->where('parentid',0)->orderBy('sorting','asc')->findAll();
        $resp_data = $this->build_tree( $data , $t );
        $merge_data = [['id'=>"0",'label'=>'Root','key'=>'root','parentid'=>"0",'default-expand-all'=>true,'children'=>$resp_data]];
        $tree_data['data'] = $merge_data;
        if ($t == 1) {
            return $tree_data;
        }
        return $this->toJson($tree_data);
    }

    public function get_dict() {
        $this->actionAuth();
        $code = $this->U('code');
        $data = LibComp::get_sub_dict( $code );
        
        return $this->toJson(["data"=>$data]);
    }

    // 左侧树状数据
    private function build_tree($data , $t = 0){
        foreach ( $data as $row ) {
            $ex = 'default-expand-all';
            $select = 'id,name as label,code as key,status,parentid,remark';
            if ($t) $select = 'id,id as value,name as label,code as key,parentid,status,remark';
            $sub_data = $this->db->select($select)->where('parentid',$row->id)->orderBy('sorting','asc')->findAll();
            $row->$ex = false;
            $child = $this->build_tree($sub_data,$t);
            $row->children = $child;
        }
        return $data;
    }

    private function _form( $Id = 0){
        $data = $this->data_tree(1);
        $form_item = [
            [ "label"=> '父级', 'field'=> 'parentid','type' => 'treeselect','placeholder'=>'请选择 父级' ,'options' => $data],
            [ 'label'=> '字典编码', 'field'=> 'code','placeholder'=>'请输入 字典编码' ,'rules'=>[[ 'required'=> true, 'message'=> '请输入 字典编码' ]]],
            [ 'label'=> '字典名称', 'field'=> 'name','placeholder'=>'请输入 字典名称' ,'rules'=>[[ 'required'=> true, 'message'=> '请输入 字典名称' ]]],
            [ 'label'=> '排序', 'field'=> 'sorting','placeholder'=>'请输入 排序' ,'rules'=>[[ 'required'=> true, 'message'=> '请输入 排序' ]]],
            [ 'label'=> '样式', 'field'=> 'css','placeholder'=>'请输入 样式' ],
            [ 'label'=> '备注','type' => 'textarea', 'field'=> 'remark','placeholder'=>'请输入 备注' ]
        ];
        return $form_item;
    }
}
