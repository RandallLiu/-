<?php
namespace App\Services;

use CodeIgniter\Model;

class opertions {
    public function log($argc = []){
        $request = \Config\Services::request();
        $URI = (new \CodeIgniter\HTTP\URI(current_url(true)))->getPath();
        $URI = (($URI == '/' || empty($URI)) ? 'main' : substr($URI,1));
        return (new \App\Models\Admin\OperationsLog())->save(
            ['source' => 'front','type'=>$request->isAJAX()?'ajax':'html', 'userid'=>session('id'),'username'=> session('name') ,'controller'=>$URI,'ip' => $request->getIPAddress(),'action'=>$URI,'uri'=>json_encode($argc)]
        );
    }
}