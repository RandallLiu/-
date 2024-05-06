<?php
namespace App\Controllers\Admin;

use App\Controllers\Base;
use App\Libraries\LibMenu;

class Powers extends Base
{
    function __construct(){
    }

    public function get_role_right(){
        $this->actionAuth(true );
        $P = $this->U();
        $db = new \App\Models\Admin\Powers();
        if( !$P['roleId'] ) return $this->setError('参数错误!');
        $data = $db->select("role_id as roleId,concat( menu_id ,'.',operation_id ) as id")->where('role_id',$P['roleId'])->findAll();
        return $this->toJson(['data'=>$data]);
    }

    // 设置功能操作
    public function set_role_right(){
        $this->actionAuth(true );
        $db = new \App\Models\Admin\Powers();
        $P = $this->U();
        if(strpos($P['id'],'.') !== false && $P['roleId']) {
            $Id = explode('.', $P['id']);
            $entity = ["role_id" => $P['roleId'], "menu_id" => $Id[0], "operation_id" => $Id[1]];
            if ($model = $db->asArray()->search($entity)->first()) {
                if($db->where('id', $model["id"])->delete()) return $this->toJson('移除成功!');
            } else {
                if ($db->save($entity)) return $this->toJson('设置成功!');
            }
        }
        return $this->setError('设置失败!');
    }
}