<?php
namespace App\Controllers\Admin;

use App\Controllers\Base;
use App\Services\comm;
use PHP_CodeSniffer\Generators\HTML;

class Roles extends Base {
    private $db;
    function __construct(){
        $this->db = new \App\Models\Admin\Roles();
    }

    public function data(){
        if ( $err = $this->actionAuth(true) )
            return $this->setError( $this->filed[$err] );

        $P = $this->U();
        $P['searchField'] = 'name,code';
        $data = $this->db->select('id,code,name,remark')
            ->search($P)->page($this->_page() , $this->_size(), true);
        return $this->toJson($data);
    }

    public function save(){
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] );
        $P = $this->U();
        if(!$P['id']) {
            $P['code'] = \App\Libraries\LibComp::guid();        $P['creatorId'] = $this->userId();
        }
        $this->db->setValidationMessages($this->db->validationMessages);
        if($this->db->save($P)){
            return $this->toJson('保存成功');
        }else{
            return $this->setError($this->db->errors());
        }
    }

    public function delete(){
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] );
        if( $this->db->delete($this->U('id')) )
            return $this->toJson('已删除');
        return $this->setError('删除失败');
    }

    public function load(){
        if ( $err = $this->actionAuth() ) return $this->setError( $this->filed[$err] );
        $comm = new comm();
        $data = $comm->get_roles_data( $this->userId() );
        return $this->toJson( ['data'=>$data] );
    }
}
