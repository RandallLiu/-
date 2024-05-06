<?php
class Db{
    private $host = '127.0.0.1';
    private $db   = 'gaokao';
    private $user = 'root';
    private $pass = '12345678';
    private $charset = 'utf8mb4';

    private $dsn;
    private $conn;
    private $error;

    public function __construct() {
        $this->dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";
        $this->conn = new PDO($this->dsn, $this->user, $this->pass);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function query($sql) {
        $stmt = $this->conn->prepare($sql);
         $stmt->execute();
        return $stmt;
    }

    public function execute($sql, $params = array()) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function first($sql , $params = []){
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 保存
    public function save($tb,$data){
        $primary_key = ""; $sql_type = "insert"; $columns = []; $sql_value = []; $update = [];
        if (array_key_exists("id",$data) && $data['id']) {
            $sql_type = "update"; $Id= $data['id']; $primary_key = "id";
        }
        foreach ($data as $key=>$value) {
            $value = preg_replace("/'/", "", $value);
            $columns[] =  $key; $sql_value[] = $value;
            $update[] = "$key='$value'";
        }
        $c = join(',',$columns); $u = join(',',$update); $vs = '';
        foreach ( $sql_value as $k=>$v ) {
            $vs .= "'{$v}'" . ($k < (count($sql_value)-1) ? ',': '');
        }
        $sql = $sql_type == "insert" ? ("$sql_type into $tb ($c) values ($vs)"): ("update $tb set $u where $primary_key = '$Id'");

        echo (date("Y-m-d:H:i:s").":$sql\n");

        return $this->conn->prepare($sql)->execute();
    }

    // 数据组装
    public function assemble($fileds, $data){
        $columns = [];
        foreach ( $fileds as $schfiled ){
            if (isset($data[$schfiled])) {
                $value = is_array($data[$schfiled]) ? json_encode($data[$schfiled], JSON_UNESCAPED_UNICODE):$data[$schfiled];
                $schools_fileds[] = $schfiled;
                $columns = array_merge($columns,[$schfiled=>$value]);
            }
        }
        return $columns;
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }

    public function endTransaction() {
        return $this->conn->commit();
    }

    public function cancelTransaction() {
        return $this->conn->rollBack();
    }

    public function errorInfo() {
        return $this->conn->errorInfo();
    }
}