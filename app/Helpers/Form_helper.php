<?php
function create_form(){
    $db = new \App\Models\Form();
    $guid = create_guid();
    if( $guid ) $db->save(['guid'=>$guid]);
    return $guid;
}

// 判断是否重复提交
function check_form_valid( $guid ){
    if ( $guid ) {
        $db = new \App\Models\Form();
        if ($db->where(['guid' => $guid, 'status' => 0])->first()) {
            return true;
        }
        return false;
    }
    return true;
}

// 销毁
function form_destroy_guid( $guid ){
    if( $guid ) {
        $db = new \App\Models\Form();
        $db->set('status', 1)->where('guid', $guid)->update();
    }
}

// 获取角色
function get_role_data( $argc = [] ){
    $db = new \App\Models\Admin\Roles();
    return $db->search($argc)->asArray()->first();
}

function find_by_array_flip($array,$find){
    $array=array_flip($array);
    return $array[$find];
}

function base64_image_data( $file ){
    $file = WRITEPATH.substr( $file ,1);
    $mime_type = mime_content_type($file);
    $base64_data= base64_encode( file_get_contents( $file ));
    $png = "data:$mime_type;base64,$base64_data";
    return $png;
}
