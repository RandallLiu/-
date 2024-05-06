<?php
namespace App\Models;
use CodeIgniter\Model;

class BaseModel extends Model{

    public function search(array $argc = []){
        if(is_object( $argc )) $argc = (array) $argc;

        foreach ( $argc as $key => $val ) {
            if ( is_array($val) ) {
                if ( count($val) > 0 && in_array($key, $this->allowedFields)) $this->whereIn($key, $val);
                continue;
            }
            if( strlen($val) > 0 ) {
                $val = trim($val);
                switch ( $key ) {
                    case 'keys' :
                        $searchField = explode(',',$argc['searchField']); $fieldCount = count($searchField);
                        $sql = ' ( ';
                        foreach ( $searchField as $k=>$field ){
                            $sql .= sprintf('%s like %s %s',$field,'\'%'. $val .'%\'',($k+1)<$fieldCount ? ' or ':'');
                        }
                        $sql .= ' ) ';
                        $this->where($sql);
                        break;
                    case 'sdate':
                        $this->where('createtime >=', '\'' . $val . '\'', false);
                        break;
                    case 'edate':
                        $this->where('createtime <=', '\'' . $val . ' 23:59:59\'', false);
                        break;
                    case 'desc':
                    case 'asc':
                        $this->orderBy($val,$key);
                        break;
                    default:
                        if( strpos($key,'.') !== false ){//else ( in_array( $pres[1] , $this->allowedFields) || $pres[1] == $this->primaryKey)
                            $this->where($key,$val);
                        }else{
                            if( in_array($key, $this->allowedFields) || $key == $this->primaryKey) $this->where($key,$val);
                        }
                        break;
                }
            }
        }
        return $this;
    }

    /**
     * whereBetween
     * @param
     * @param $key
     * @param array $value
     * */
    protected function whereBetween($key,$value = []){
        return $this->where('('. $key .' >= '. $value[0] .' and '. $key .' <= '. $value[1] .')');
    }

    /**
     * 保存
     * */
    public function save($data): bool{
        if (empty($data)) {
            return true;
        }

        if (is_object($data) && isset($data->{$this->primaryKey}) && !empty($data->{$this->primaryKey})) {
            $response = $this->update($data->{$this->primaryKey}, $data);
        }
        elseif (is_array($data) && ! empty($data[$this->primaryKey])) {
            $response = $this->update($data[$this->primaryKey], $data);
        }
        else {
            $response = $this->insert($data, false);
            if ($response !== false)
            {
                $response = true;
            }
        }

        return $response;
    }

    /**
     * 获取分页数据
     * */
    public function paginates(int $page = 1,int $perPage = 10){
        return ['data'=>$this->paginate($perPage,'default',$page),'total'=>$this->total()];
    }

    /**
     * 获取分页数据
     * */
    public function pagination(int $page = 1,int $perPage = 10){
        $data = $this->asObject()->paginate($perPage,'default',$page);
        foreach ( $data  as $key=>$row ){
            $num = ((($page - 1) * $perPage) + ($key + 1));if ( is_object( $row ) ) $row->rownum = $num; else $row['rownum'] = $num;
        }
        return ['data'=>$data,'iTotalDisplayRecords'=>$this->total()];
    }

    /*
     * 获取分页数据(带行号)
     * @param int $page 当前页面
     * @param int $perPage 每页分页大小
     * @param array $sort 排序
     *
     * @return array
     * */
    public function RPaging(int $page = 1,int $perPage = 10){
        $data = $this->asObject()->paginate($perPage,'default',$page);
        $total = $this->total();
        foreach ( $data  as $key=>$row ){
            $num = ((($page - 1) * $perPage) + ($key + 1));
            if ( is_object( $row ) ) $row->rownum = $num; else $row['rownum'] = $num;
        }
        return ['data'=>$data,'iTotalDisplayRecords' => $total];
    }

    /**
     * 获取分页数据
     * @param int $page 当前页面
     * @param int $perPage 每页分页大小
     * @param array $sort 排序
     *
     * @return array
     * */
    public function page(int $page = 1,int $perPage = 10, $has_rownum = false){
        $data = $this->paginate($perPage,'default',$page);
        $total = $this->total();
        if ( $has_rownum )
            foreach ( $data  as $key=>$row ){
                $num = ((($page - 1) * $perPage) + ($key + 1));if ( is_object( $row ) ) $row->rownum = $num; else $row['rownum'] = $num;
            }
        return  ['data'=>$data,'total'=>$total ];
    }


    /**
     * 获取总记录数
     * */
    protected function total(){
        return $this->pager->getDetails()['total'];
    }

    //
    public function whereAuth( $key = 'village_id'){
        $Ids =  where_auth() ;
        if( $Ids && $key)  $this->whereIn($key, $Ids);
        return $this;
    }

    //业务存储过程调用接口
    function callPro($proName,$proParams){
        switch($proName){
            default:
                $proParamsSort=$proParams;
                break;
        }
        if (!is_null($proParamsSort)){
            $paramList = "";
            foreach ($proParamsSort as $paramElement){
                $paramList .= $this->db->escape($paramElement) . ",";
            }
            $paramList = substr($paramList,0,strlen($paramList)-1);
            $sql = "call $proName($paramList)";
        }
        else {
            $sql = "call $proName()";
        }
        $query = $this->db->query($sql);
        $arr= $query->getResult('array');
        $this->db->close();

        $rtn=array();

        if(count($arr) == 1 && array_key_exists('retCode',$arr[0])){
            while(list($k,$v)=each($arr[0])){
                $rtn[$k]=$v;
            }
            return $rtn;
        }
        return $arr;
    }

    function callfn($fn,$fn_params){
        $params = is_array( $fn_params ) ? join(',',$fn_params) : $fn_params;
        $return = ($this->db->query("select {$fn}({$params}) as name")->getRow())->name;
        return $return;
    }
}
