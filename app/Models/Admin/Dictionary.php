<?php namespace App\Models\Admin;

use CodeIgniter\Database;
use CodeIgniter\Model;
class Dictionary extends \App\Models\BaseModel
{
    protected $table = 'admin_dictionary';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name','code','parentid','sorting','status','css','remark','hidden'];

    protected $returnType = 'App\Entities\Forms';

    protected $validationRules = [
        'code'     => 'required|is_unique[admin_dictionary.code,id,{id},parentid,{parentid}]',
    ];
    protected $validationMessages =[
        'code'=>[
            'required'=> '请输入用户名称',
            'is_unique'=> '字段已被占用!'
        ],
    ];

    protected $beforeUpdate = ['set_data_before'];
    protected $beforeInsert = ['set_data_before'];
    protected $afterDelete = ['after_delete'];

    protected function set_data_before(array $array){
        log_message('error','before:'.json_encode($array));
        $max = $this->selectMax('sorting')->where('parentid',$array['data']['parentid'])->where('id<>',$array['id'][0])->asArray()->first();
        if( !$array['id'][0] || !$array['data']['sorting'] ) {
            $array['data']['sorting'] = ( $max['sorting'] ? ( intval( $max['sorting'] ) + 1 ) : 1 );
        }
        return $array;
    }

    protected function after_delete($data){
        if( $data['id'][0] ) {
            // 删除操作记录
            //$this->where('parentid',$data['id'][0])->delete();
        }
        return $data;
    }
}
