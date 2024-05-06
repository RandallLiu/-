<?php
namespace App\Controllers\Admin;

use App\Controllers\Base;
class Users extends Base
{
    protected $db;
    function __construct(){
        $this->db = new \App\Models\Admin\Users();
    }

    // 数据
    public function data(){
        $this->actionAuth(true);
        $P = $this->U();
        $P['searchField'] = 'username,phone,name';
        $data = $this->db->asObject()->select('id,username,name,phone,status,remark')
            ->search($P)->paginates($this->_page(),$this->_size());
        $db = new \App\Models\Admin\Roles();
        foreach ( $data['data'] as $item ) {
            $role_data = $db->where("id in ( select role_id from admin_users_role where user_id = {$item->id})")->findAll();
            $arr = [];
            foreach ( $role_data as $row ) $arr[] = $row['name'];
            $item->roles = join(',',$arr);
        }
        return $this->toJson($data);
    }

    // 编辑
    public function detail(){
        if ( $err = $this->actionAuth(true) )
            return $this->setError( $this->filed[$err] );
        $db = new \App\Models\Admin\UsersRoles();
        $Id = $this->U('id');
        if ( $Id && !$user_data = $this->db->select('id,username,access_data,name,phone,remark')->where('id',$Id )->first()) {
            return $this->setError('参数错误');
        }
        $data = [];
        if ( $Id ) {
            $ids = []; $ur_data = $db->select()->where('user_id', $Id)->findAll();
            foreach ($ur_data as $item) $ids[] = $item['role_id'];
            $user_data["roles"] = $ids; $user_data['access_data'] = $user_data['access_data']?json_decode($user_data['access_data']):[];
        }
        $data['form_item'] = $this->_form($Id);
        $data['data'] = $user_data ?? [];
        return $this->toJson($data);
    }

    // 保存
    public function save(){
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] );
        $form = $this->U();
        if ( !$form['id'] && !($this->db->where('id',$form['id'])->first()) ) $form['password'] = '888888';
        $form['access_data'] = $form['access_data'] ? json_encode($form['access_data']) : '';
        $this->db->setValidationMessages($this->db->validationMessages);
        if( $this->db->save($form) ) {
            $db = new \App\Models\Admin\UsersRoles();
            $form["id"] = $form["id"]?:$this->db->getInsertID();
            // 保存用户角色
            $db->batch_save($form["id"] , $form["roles"]);
            return $this->toJson('已保存');
        }
        return $this->setError($this->db->errors());
    }

    // 删除
    public function delete(){
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] ,$err);
        $Id = $this->U('id');
        if( $this->db->where('id',$Id)->delete() ) {
            return $this->toJson('已删除');
        }
        return $this->setError('删除失败');
    }

    // 密码重置
    public function spasswd(){
        $this->actionAuth(true);
        $v = $this->U('value');
        if ( $data = $this->db->where('id',$this->U('id'))->first()) {
            $data->password = $v;
            if($this->db->save($data))
                return $this->toJson('密码已重置为:'.$v);
        }
        return $this->setError('重置失败');
    }

    // 设置启用/禁用
    public function disenabled(){
        $this->actionAuth(true);
        if ( $data = $this->db->where('id',$this->U('id'))->first()) {
            if($this->db->where('id',$data['id'])->set('status',!$data['status'])->update())
                return $this->toJson("已成功设置");
        }
        return $this->setError("设置错误!");
    }

    // 修改密码
    public function passwd(){
        $this->check_login_vip(true);
        return $this->render([
            'view_path' => '/Home/passwd'
        ]);
    }

    // 重置密码
    public function setpasswd(){
        $this->check_login_vip(true);
        $U = $this->U();
        if( session('id') == 0 ) return $this->setError('系统管理改不了密码!');
        $model = $this->db->find(session('id'));
        if(md5($U['o'])  != $model->password) return $this->setError('原密码不正确!');
        if($U['n'] != $U['r']) return $this->setError('两次原密码输入不正确!');

        if( $this->db->save( ['id'=>session('id'),'password'=>md5( $U['n'] )] ) ){
            return $this->toJson('密码已设置!');
        }
        return $this->setError('密码设置失败');
    }
}
