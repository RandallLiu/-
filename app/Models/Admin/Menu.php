<?php namespace App\Models\Admin;

use CodeIgniter\Database;
use CodeIgniter\Model;

class Menu extends \App\Models\BaseModel{
    protected $table = 'admin_menu';
    protected $primaryKey = 'id';
    protected $allowedFields = ['parentid','title','path','icon','name','component','redirect','linkUrl','isLink','sort','status','isAffix','isIframe','hidden','remark'];

    protected $beforeUpdate = ['action_before'];
    protected $beforeInsert = ['action_before'];
    protected $afterDelete = ['after_delete'];

    protected function after_delete($data){
        if( $data['id'][0] ) {
            // 删除功能 操作
            $this->from('admin_operation',true)->where('menuid',$data['id'][0])->delete();
            // 删除权限 操作
            $this->from('admin_power',true)->where('menu_id',$data['id'][0])->delete();
        }
        return $data;
    }

    protected function action_before(array $array){
        //$max = $this->selectMax('sort')->where('parentid',$array['data']['parentid'])->asArray()->first();
        if(isset($array['data']['parentid']) && !$array['id'] || !$array['data']['sort'] ) {
            //$array['data']['sort'] = ( $max['sort'] ? ( intval( $max['sort'] ) + 1 ) : 1 );
        }
        return $array;
    }

    //获取用户菜单权限
    public function get_menu_user_rights($userid,$menuid){
        $this->select('c.id as rightId,c.name,c.menuid as muduleId,c.uri')
            ->from('admin_users_role a',true)
            ->join('admin_power b','a.role_id=b.role_id')
            ->join('admin_operation c','b.menu_id=c.menuid and b.operation_id = c.id')
            ->where('a.user_id',$userid)
            ->where('c.menuid',$menuid)
            ->groupBy('c.id,c.name,c.uri,c.menuid');
        $result = $this->asObject()->findAll();

        return $result;
    }

}
