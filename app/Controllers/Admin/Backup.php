<?php

namespace App\Controllers\Admin;

use App\Controllers\Base;

class Backup extends Base {

    private $tables = array();
    private $path;
    private $dbname;
    private $model;

    function __construct(){
        $this->model = db_connect();
        $this->path = WRITEPATH.'backup/';
        $this->dbname = env('database.default.database');
        $this->check_path();
        $this->get_tables();
    }

    //备份方法
    public function backupAll (){
        $this->actionAuth(true);
        $filename = $this->path . time() .'.sql';
        $tbs = [];
        if( $this->tables ) {
            $data = $this->genTitle();
            file_put_contents($filename, $data);
            foreach ( $this ->tables as $table ) {
                if(!in_array( $table, $tbs)) {
                    $ctable = $this->get_create_table($table);
                    $data = $this->get_table_structure($ctable[0]);
                    file_put_contents($filename, $data, FILE_APPEND);
                    $data = $this->get_table_records($table);
                    file_put_contents($filename, $data, FILE_APPEND);
                }
            }

            //$filename = $this->path . time() .'.sql';

            //if(file_put_contents($filename, $data,FILE_APPEND)){
            return $this->toJson();
//            }else{
//                return $this->setError('备份失败');
//            }
        }
    }

    public function restore ($file){
        $this->actionAuth(true);
        $filename = $file;
        if( !file_exists($filename) ) {
            return false;
        }
        $str = fread( $hd = fopen($filename, "rb") , filesize($filename) );
        $sqls = explode(";\r\n", $str);//拆分
        if($sqls) {
            foreach($sqls as $sql) {
                $this->model->query($sql)->getResult();
            }
        }
        fclose($hd);
        
        return true;
    }
    
    public function index(){
        $this->actionAuth(true);
        return $this->display();
    }

    public function getFileInfo(){
        $this->actionAuth(true,'admin/backup/index');
        $temp = array();$num = 0;

        if( is_dir($this->path) ) {
            $handler = opendir($this->path);
            while ( $file = readdir($handler) ){
                if( $file !== '.' && $file !== '..' ) {
                    $filename = $this->path.$file;
                    $temp[$num]['name'] = $file;
                    $temp[$num]['size'] = ceil(filesize($filename)/1024);
                    $temp[$num]['time'] = date("Y-m-d H:i:s" ,filemtime($filename));
                    $temp[$num]['path'] = 'backup/';
                    $num ++;
                }
            }
        }
        rsort($temp);
        $data['data'] = $temp;
        $data['iTotalDisplayRecords'] = $num;
        return $this->toJson($data);
    }

    //删除文件
    public function delFile (){
        $this->actionAuth();
        $file = $this->request('file');
        $path = WRITEPATH.'backup/'.$file;

        if( file_exists($path) ) {
            if(unlink($path)) return $this->toJson();
        }
        return $this->setError('删除失败');
    }

    private function genTitle()
    {
        $time = date("Y-m-d H:i:s" ,time());
        $str = "/*************************\r\n";
        $str.= " * {$time} \r\n";
        $str.= " ************************/\r\n";
        $str.= "SET FOREIGN_KEY_CHECKS=0;\r\n";
        return $str;
    }

    private function get_tables (){
        $sql = 'show tables';
        if( $data = $this->model->query($sql)->getResult() ) {
            foreach ( $data as $val ) {
                $val = (array) $val;
                $this->tables[] = $val['Tables_in_'.$this->dbname];
            }
        }
    }

    private function get_create_table ($table) {
        $sql = "show create table $table";
        $arr = $this->model->query($sql)->getResult();
        return array_values($arr);
    }

    private function get_table_structure ($ctable)
    {
        $table = $ctable->Table;$ct = 'Create Table';
        $str  = "-- ----------------------------\r\n";
        $str .= "-- Table structure for `{$table}`\r\n";
        $str .= "-- ----------------------------\r\n";
        $str .= "DROP TABLE IF EXISTS `{$table}`;\r\n".$ctable->$ct.";\r\n\r\n";
        return $str;
    }

    private function get_table_records ($table)
    {
        $sql = "select * from {$table}";
        if( $data = $this->model ->query($sql)->getResult() ) {
            $str = "-- ----------------------------\r\n";
            $str.= "-- Records of $table \r\n";
            $str.= "-- ----------------------------\r\n";

            foreach ( $data as $val ) {
                if( $val ) {
                    $keyArr = array();
                    $valArr = array();
                    foreach ( $val as $k => $v ) {
                        $keyArr[] = "`".$k."`";
                        //对单引号和换行符进行一下转义
                        $valArr[] = "'".str_replace( array("'","\r\n"), array("\'","\\r\\n"), $v )."'";
                    }
                    $keys = implode(', ', $keyArr);
                    $values = implode(', ', $valArr);
                    $str .= "INSERT INTO `{$table}` ($keys) VALUES ($values);\r\n";
                }
            }
            $str .= "\r\n";
            return $str;
        }
        return '';
    }

    private function check_path (){
        if( !is_dir($this->path) ) {
            mkdir($this->path ,0777 ,true);
        }
    }
}