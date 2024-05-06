<?php
namespace App\Controllers\Admin;

use App\Controllers\Base;
use App\Libraries\LibMenu;
use App\Services\comm;
use Config\Services;
class Menus extends Base {
    private $db;
    
    public function __construct(){
        $this->db = new \App\Models\Admin\Menu();
    }

    // 数据列表
    public function data(){
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] );

        $P = $this->U();
        $P['searchField'] = 'title,name';
        // 数据列表
        $data = $this->db->where('parentid',0)->search($P)->asObject()->orderBy('sort','asc')->paginates($this->_page());
        $data['data'] = LibMenu:: build_tree_data( $data['data'] ,$P);
        return $this->toJson($data);
    }

    // 级联上级菜单
    public function cascader_menu_data(){
        $this->actionAuth();
        $menu_data = $this->db->select('id,id as value,title as label')->where('parentid',0)->asObject()->orderBy('sort','asc')->findAll();
        $root[] = ["id"=>"0","value"=>"0","label"=>'根目录'];
        $menus = LibMenu:: build_cascader_menu_data( $menu_data );
        $data['data'] = array_merge($root,$menus);
        return $this->toJson($data);
    }
    
    // 保存菜单
    public function save(){
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] );
        $P = $this->U();
        if ($this->request->getMethod() == 'post') {
            if (!$P) return $this->setError('保存失败');
            if ($P['parentid']) $P['parentid'] = array_reverse($P['parentid'])[0];
            $P['hidden'] = $P['hidden'] ? 1 : 0;
            $P['isKeepAlive'] = !intval($P['isKeepAlive']);
            $P['isAffix'] = ($P['isAffix']) ? 1 : 0;
            $P['isLink'] = ($P['isLink']) ? 1 : 0;
            $P['isIframe'] = ($P['isIframe']) ? 1 : 0;
            log_message('error', json_encode($P));
            if ($P && $this->db->save($P)) {
                $db = new \App\Models\Admin\Actions();
                // 获取保存后菜单ID
                $P['id'] = ($P['id']) ?: $this->db->getInsertID();
                // 批量保存菜单功能权限
                $db->batch_save($P['id'], $P['actions']);
                // 保存成功
                return $this->toJson('保存成功');
            }
        }

        return $this->setError($this->db->errors() ??'保存失败');
    }

    public function detail(){
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] );
        $Id = $this->U('id');
        if ($Id && $menu_data = $this->db->asObject()->select(  'id,parentid,title,path,icon,name,component,redirect,linkUrl,isLink,sort,status,isAffix,isIframe,isKeepAlive,hidden,remark')->where('id',$Id)->first() ) {
            $menu_data->parentid = [($menu_data->parentid)];
            $menu_data->actions = $this->db->select('id,name,uri')->from('admin_operation',true)->where('menuid',$Id)->findAll();
            return $this->toJson(['data'=>$menu_data]);
        }
        return $this->setError('获取信息失败');
    }

    // 删除 功能操作
    public function delaction(){
        $this->actionAuth(true);
        $db = new \App\Models\Admin\Actions();
        if ($db->delete($this->U('id'))) {
            return $this->toJson('已经删除!');
        }
        return $this->setError('删除失败');
    }

    // 删除菜单
    public function delete(){
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] );

        $db = $this->db;
        if ($db->delete($this->U('id'))) {
            return $this->toJson('已经删除!');
        }
        return $this->setError('删除失败');
    }
    
    // 自动获取 controller 方法
    public function get_controller_action(){
        $this->actionAuth();
        $controller = $this->request('controller');
        $className = 'App\Controllers'.'\\'.$controller;
        $a = new \ReflectionClass($className);
        $b = $a->getMethods();
        $views = '';
        foreach ($b as $item){
            if($item->class == $className && $item->isPublic())
                $views .= view('/Admin/Menus/action',
                    ['model'=>
                        (object)[
                            'id'=>'',
                            'name'=>'',
                            'code'=>$item->name,
                            'uri'=>(str_replace('\\,/',$controller).'/'.$item->name)]
                    ]);
        }
        return $views;
    }


    public function disenabled(){
        if ( $err = $this->actionAuth(true) ) return $this->setError( $this->filed[$err] );

        $Id= $this->U('id');
        if ( $Id && $data = $this->db->where('id',$this->U('id'))->first() ) {
            if ( $this->db->set('status',($data['status']=="1"?"0":"1"))->update($data['id']) ) {
                return $this->toJson('操作成功');
            }
        }
        return $this->setError('操作失败');
    }

    public function get_authorize(){
        $db = new \App\Models\Admin\Menu();
        $data = menu_power_build($this->userId());//menu_tree($menu,$this->userId());
        return $this->toJson(['data'=>$data]);
    }

    public function get_menu(){
        $P = $this->U();
        $tree_data = $this->_build_menus_tree($this->userId());
        return $this->toJson(['data'=>$tree_data]);
    }

    // 登录时获取菜单
    private function _build_menus_tree( $userId , $hidden = [0] ){
        $db = new \App\Models\Admin\Menu();
        $db->select('id,title,parentid,path,icon,name,linkUrl,isLink,isAffix,isIframe,hidden,component,redirect,sort')->whereIn('status',$hidden);
        if ( $userId ) {
            $data = $db->where("id in (select distinct menu_id from admin_power where role_id in (select distinct role_id from admin_users_role where user_id=$userId))")
                ->orderBy('sort', 'asc')->asArray()->findAll();
        } else {
            $data = $db->distinct()->orderBy('sort', 'asc')->asArray()->findAll();
        }

        function find_tree($data,$level = 0){
            $db = new \App\Models\Admin\Menu();
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

        function bm( $data, $all , $userId){
            $child = $tree_data = [];

            $tree_data["path"] = $data["path"];
            $tree_data["name"] = $data["title"];
            $tree_data["component"] = "{$data["component"]}";
            if ( $data["redirect"] ) $tree_data["redirect"] = $data["redirect"];
            $rights = get_menu_action($data["id"], $userId ,( $userId == 0 ? true : false));

            $tree_data["meta"] = [
                'id' => $data['id'],
                'title' => $data["title"],
                'isLink' => $data["linkUrl"],
                'isHide' => $data["hidden"]?true:false,
                'isKeepAlive' => !$data["isKeepAlive"],
                'isAffix' => $data["isAffix"]?true:false,
                'isIframe' => $data["isIframe"]?true:false,
                'icon' => $data["icon"],
            ];

            if ( $rights ) {
                $items = [];
                foreach ( $rights as $right ){
                    $items[] = [
                        'id' => $right->muduleId.'.'.$right->rightId,
                        'title' => $right->name,
                        'uri' => $right->uri
                    ];
                }
                if ($items) $tree_data["meta"]["rights"] = $items;
            }

            foreach ( $all as $row ) {
                if( $data['id'] == $row['parentid'] ) {
                    $child[] = bm( $row , $all , $userId );
                }
            }

            if ( $child ) $tree_data['children'] = $child;

            return $tree_data;
        }

        $tree[] = [
                'path' => '/home',
                'name' => 'home',
                'component' => '../views/home/index.vue',
                'meta' => [
                    'id' => 0,
                    'title' => 'message.router.home',
                    'isLink' => '',
                    'isHide' => false,
                    'isKeepAlive' => true,
                    'isAffix' => true,
                    'isIframe' => false,
                    'icon' => 'iconfont icon-shouye',
                ]
            ];

        foreach ( $root as $item ){
            $tree[] = bm($item,$ALL,$userId);
        }
        return ($tree);
    }

    public function power_menus_rights(){
        $tree_data = menu_power_build(0);
        return $this->toJson(['data'=>$tree_data]);
    }
}