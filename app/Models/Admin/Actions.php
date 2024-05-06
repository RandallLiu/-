<?php namespace App\Models\Admin;

use CodeIgniter\Model;

class Actions extends \App\Models\BaseModel {
    protected $primaryKey = 'id';
    protected $table = 'admin_operation';
    protected $allowedFields = ['menuid','name','code','uri'];
    protected $returnType = 'App\Entities\Forms';
    protected $afterDelete = ['after_delete'];

    protected function after_delete($data){
        if( $data['id'][0] ) {
            // 删除操作记录
            $this->from('admin_power',true)->where('opration_id',$data['id'][0])->delete();
        }
        return $data;
    }

    // 批量保存
    public function batch_save( $menuid , $data ){
        if( !$data ) return false;
        $this->set('state',1)->where('menuid',$menuid)->update();
        $menu = [];
        foreach ( $data as $k => $v ) {
            if ($this->find($v)) {
                $this->update($v, ['state' => 0, 'name' => $v['name'], 'uri' => $v['uri']]);
            } else {
                if ($this->where(['menuid' => $menuid, 'uri' => $v['uri']])->first()) {
                    $this->where(['menuid' => $menuid, 'uri' => $v['uri']])
                        ->update(['state' => 0, 'name' => $v['name'], 'uri' => $v['uri']]);
                } else {
                    if ($v['name'])
                        $menu[] = array(
                            'menuid' => $menuid,
                            'name' => $v['name'],
                            'uri' => strtolower($v['uri'])
                        );
                }
            }
        }
        if ( $menu ) $this->insertBatch( $menu);
    }
}
