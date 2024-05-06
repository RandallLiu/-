<?php

namespace App\Models\Admin;

class UsersRoles extends \App\Models\BaseModel {
    protected $table = 'admin_users_role';
    protected $allowedFields = ['user_id','role_id'];

    public function batch_save( $userId , $data ){
        if ( !($data)) return false;
        $ur = [];
        $this->where('user_id',$userId)->delete();
        foreach ( $data as $v ) {
            if ($v) $ur[] = ['user_id' => $userId, 'role_id' => $v];
        }
        if( $ur ) $this->insertBatch($ur);
        return true;
    }
}
