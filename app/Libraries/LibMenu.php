<?php

namespace App\Libraries;

use App\Services\comm;

class LibMenu{
    // 树形结构
    public static function build_tree_data( $data ,$searchFiled = [] ){
        $db = new \App\Models\Admin\Menu();
        foreach ( $data as $row ) {
            $sub_data = $db->select('*,')->asObject()->search( $searchFiled )->where('parentid',$row->id)->orderBy('sort','asc')->findAll();
            $row->children = LibMenu::build_tree_data($sub_data);
        }
        return $data;
    }

    public static function build_cascader_menu_data( $data ){
        $db = new \App\Models\Admin\Menu();
        foreach ( $data as $row ) {
                $sub_data = $db->asObject()->select('id,id as value,title as label')->where('parentid',$row->id)->orderBy('sort','asc')->findAll();
            $row->children = LibMenu::build_cascader_menu_data($sub_data);
        }
        return $data;
    }



    // 初始化菜单
    public static function init_menu(){
        $sub_menu = '';
        $GET_URI = new \CodeIgniter\HTTP\URI(current_url(true));
        $URI = substr($GET_URI->getPath(),1);
        // 判断
        if ( in_array($URI,['colleges/index']) ) {
            // $sub_menu = view('/Home/submenu_colleges_index');
        }

        // 判断
        if ( in_array($URI,['special/index']) ) {
            // $sub_menu = view('/Home/submenu_specials');
        }
        return $sub_menu;
    }

    // 获取操作功能代码
    public static function get_action(){
        $session = \CodeIgniter\Config\Services::session();
        if ( $session->has('id') && !$session->actions ) {
            $db = new \App\Models\Admin\Actions();
            $data = $db->select('code')->distinct()->findAll();
            foreach ( $data as $item ) {
                $arr[] = $item->code;
            }
            $session->set('actions', $arr);
        } else {
            $arr = $session->actions;
        }
        return $arr;
    }
}
