<?php
require_once "db.php";
try{
    $db = new Db();
    // $result = $db->save("admin_login_logs",['id'=>2,"userId" => 1,'username'=>'张三2','ip'=>'127.0.0.1','ua' => 'bb']);
    $result = $db->first("select * from admin_login_logs where id = ?",[1]);
    var_dump($result);
}catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}