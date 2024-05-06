<?php namespace App\Models\Admin;

use CodeIgniter\Database;
use CodeIgniter\Model;

class Roles extends \App\Models\BaseModel
{
    protected $table = 'admin_roles';

    protected $primaryKey = 'id';

    //protected $returnType = 'App\Entities\Forms';

    protected $validationRules = [
        'code'  =>        'required|is_unique[admin_roles.code,id,{id}]',
        'name'  =>        'required|is_unique[admin_roles.name,id,{id}]'
    ];
    protected $validationMessages = [
        'code'  =>  ['is_unique'     =>      '角色编码已存在!'],
        'name'  =>  ['is_unique'     =>      '角色名称已存在!']
    ];
    protected $allowedFields = ['code','name','remark','creatorId'];
    protected $afterDelete = ['delete_after'];
    
    // 删除角色
    public function delete_after($data){
        if($data['id'][0]) {
            // 删除用户角色
            $this->from('admin_users_role', true)->where('role_id', $data['id'][0])->delete();
            // 删除权限操作
            $this->from('admin_power', true)->where('role_id', $data['id'][0])->delete();
        }
        return $data;
    }
}