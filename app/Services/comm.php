<?php
namespace App\Services;

use App\Libraries\LibComp;
use App\Libraries\LibMenu;
use App\Models\Admin\Menu;
use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Model;

class comm {

    protected $temp = [];

    // 获取角色列表
    public function get_roles_data( $userId = 0 ){
        $db = new \App\Models\Admin\Roles();
        $data = ( $userId ) ? $db->select('id as value,name as label')->where("id in (select role_id from admin_users_role where user_id = $userId)")->asArray()->where('creatorId<>',$userId)->findAll(): [];

        $data2 = $db->select('id as value,name as label')->where('creatorId',$userId?:0)->asArray()->findAll();
        return array_merge($data,$data2);
    }

    // 获取指定角色
    public function get_access_roles( $userId = 0 , $access = ['all'] ){
        $db = new \App\Models\Admin\Roles();
        if ( $userId ) $db->where('creatorId', $userId );
        $data = $db->whereIn('asscess',$access)->asArray()->findAll();
        return $data;
    }

    // 查找是否有功能( 操作权限 )
    public function check_auth( $uri , $userId = 0 ){
        $db = new \App\Models\Admin\Actions();
        $data = $db->select('id')
            ->where("id in (select operation_id from admin_power where role_id in (select role_id from admin_users_role where user_id = '$userId'))")
            ->where('uri',$uri)
            ->first();
        return ( (session('id') == 0) || session('power') == 'all' || $data ) ? true : false;
    }

    // 菜单( 树形菜单 )
    public function build_menus_tree( $userId = 0, $hidden = [0] ){
        $db = new Menu();
        $db->whereIn('hidden',$hidden);
        if ( $userId ) {
            $data =$db->where(" id in (select menu_id from admin_power where role_id in (select role_id from admin_users_role where user_id = '$userId')) ")
                    ->orderBy('sort', 'asc')
                    ->asArray()
                    ->findAll();
        }
        else {
            $data = $db
                ->distinct()
                ->where('(parentid>0 or length(url)>1)')
                ->orderBy('sort', 'asc')
                ->asArray()
                ->findAll();
        }

        function find_tree($data,$level = 0){
            $db = new Menu();
            $up_data = $up_fd =[];
            // 判断父级结点不为root时
            if( $data && $data['parentid'] > 0 ){
                $fd = $db->where('id',$data['parentid'])->asArray()->first();
                // 查找父级
                $up_fd = find_tree($fd,($level + 1));
            }
            $up_data[] = $data;

            return  (array_merge($up_data,$up_fd));
        }

        $find_data = [];

        foreach ( $data as $row ) {
            $find_data[] = find_tree($row,1);
        }

        $root = [];$T = []; $ALL = [];

        foreach ( $find_data as $item ) {
            $a = array_filter($item,function ($r) {
                return $r['parentid'] == 0 ? $r : [];
            });
            foreach ($a as $v) {
                if (!in_array($v['title'], $T)) {
                    $T[] = $v['title'];
                    $root[] = ($v);
                }
            }
            $b = array_filter($item,function ($r) {
                return $r;
            });
            foreach ($b as $k=>$v) {
                $ALL[] = $v;
            }
        }

        function bm( $data, $all ){
            $child = [];
            foreach ( $all as $row ) {
                if( $data['id'] == $row['parentid'] ) {
                    $child[] = bm($row,$all);
                }
            }
            $data['children'] = $child;
            return $data;
        }
        $tree = [];
        foreach ( $root as $item ){
            $tree[] = bm($item,$ALL);
        }
        // 排序
        usort($tree, function ($a, $b) {
            $al = $a['sort'];
            $bl = $b['sort'];
            if ($al == $bl)
                return 0;
            return ($al < $bl) ? -1 : 1;
        });
        return ($tree);
    }

    // 深层合并
    function array_merge_deep(...$arrs) {
        $merged = [];
        while ($arrs) {
            $array = array_shift($arrs);
            if (!$array)  continue;

            foreach ($array as $key => $value) {
                if (is_string($key)) {
                    if (is_array($value) &&
                        array_key_exists($key, $merged) &&
                        is_array($merged[$key])) {
                        $merged[$key] = $this->array_merge_deep(...[$merged[$key], $value]);
                    } else {
                        $merged[$key] = $value;
                    }
                } else {
                    $merged[] = $value;
                }
            }
        }
        return $merged;
    }
}
