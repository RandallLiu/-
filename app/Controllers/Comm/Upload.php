<?php
namespace App\Controllers\Comm;

use App\Libraries\LibComm;
use App\Models\Admin\Menu;
use App\Controllers\Base;
use CodeIgniter\Services;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;
use think\Image;

class Upload extends Base{
    //  默认10M文件
    protected $filesize = 10*1024*1024;
    //  默认上传格式
    protected $allowedfile = ['txt','jpg','jpeg','gif','png','xls','xlsx','pdf','zip','rar','mp3','doc','docx'];
    // 图片格式
    protected $images = ['jpg','jpeg','gif','png'];

//    protected $AK = 'O47R_VMzkZTHn_5COc0mFNk_OLk3QvsRpLzXL2Em';
//    protected $SK = 'uqL0KVKb2ep4THFuGGSNJQxwjZTrrb7mfUpes5WU';

    // 文件上传
    public function dofile( $action = '' ){
         if ( $err = $this->actionAuth() ) return $this->setError( $this->filed[$err] ,$err);
        $resp = ['code'=>false,'msg'=>'上传失败!','url'=>'','error'=>1];
        if($this->request->getMethod() === 'post'){
            $file = $this->request->getFile('file');

            if(!in_array($file->guessExtension(),$this->allowedfile)){
                return $this->toJson(['error'=>1,'url'=>'上传格式有误!']);
            }

            if($file->getSize()>$this->filesize){
                return $this->toJson(['error'=>1,'url'=>'文件上传过大!']);
            }

            if( $file->isValid() && !$file->hasMoved() ){
                $vm_path = 'uploads/'.$file->getExtension().'/'.date('Ymd').'/';
                $real_path = WRITEPATH.$vm_path;
                $filename = \App\Libraries\LibComp::guid().'.'.$file->getExtension();
                if (!file_exists($real_path)) {
                    mkdir($real_path, 0777, true);
                }
                $file->move($real_path, $filename);
                // 上传文件处理
                // $do_file = $this->_action( $action,$vm_path,$filename);
                // 上传到七牛CDN
                if ( $action == 'qiniu' && in_array( $file->guessExtension() , $this->images)) {
                    $url = $this->_qiniu_upload("$real_path$filename", $filename);
                } else {
                    $url = "/$vm_path$filename";
                }
                // 返回文件
                $resp = ['code'=>true,'msg'=>'上传成功!','url'=>$url,'error'=>0,'errno'=>0,'data'=>["url"=>$url]];
            }else {
                return $this->setError("{$file->getErrorString()}({$file->getError()})");
            }
        }
        return $this->toJson($resp);
    }

    // 上传附件
    public function do( $action ){
        return $this->dofile( $action );
    }

    // 文件再处理
    private function _action( $action, $file_path , $filename = ''){
        $file = "/".$file_path.$filename;
        $path = WRITEPATH.$file_path;
        switch ( $action ) {
            case 'signet':  // 上传章戳
                break;
            case 'stamp':   // 上传预盖章文件
                $fileinfo  = new \CodeIgniter\Files\File( ".$file" );
                $file_ext = strtolower( $fileinfo->getExtension() );

                // 处理PDF 文件
                if ( $file_ext == 'pdf' ) {
                    // $path = WRITEPATH.substr($file_path,1,strlen($file_path));
                    // pdf 转成 png
                    // $file_path = LibComm::pdf_to_png(WRITEPATH.$file_path.$filename, $path  );
                    // 最终文件路径
                    // $file = str_replace(WRITEPATH,'/',$file_path);
                }

                // 处理 officer
                if ( in_array( $file_ext , ['doc','docx','xls','xlsx'] )) {
                    $name = str_replace($file_ext,'pdf',$filename);
                    $file = WRITEPATH.substr($file,1,strlen($file));
                    // 转换成 pdf 文件
                    /*
                    exec('unoconv -f pdf "'.$file.'" 2>&1',$resp,$out);
                    if ( !$resp ) {
                        // pdf 转成 png
                        $file_path = LibComm::pdf_to_png(WRITEPATH.$file_path.$name, $path );
                        // 最终文件路径
                        $file = str_replace(WRITEPATH,'/',$file_path);
                    }*/
                }

                // 处理图片 文件
                if (in_array( $file_ext ,$this->images )) {
                    // log_message('error',$file);
                    // $width = $image->getWidth(); $height = $image->getHeight();
                    // chmod(WRITEPATH.$file_path.$filename,0755);
                    // 转换成1024像数
                    // $image->resize($width,$height)->save(".$file",70);
                }
                break;
            default:

                break;
        }
        return $file;
    }

    private function _qiniu_upload( $file_path, $filename ){
        $bucket = 'ytydh';
        // 构建鉴权对象
        $auth = new Auth($this->AK,$this->SK);

        // 生成上传 Token
        $token = $auth->uploadToken($bucket);
        // 初始化 对象并进行文件的上传。
        $uploadMgr = new UploadManager();
        // 调用 UploadManager 的 putFile 方法进行文件的上传。
        list($resp, $err) = $uploadMgr->putFile($token, $filename, $file_path, null, 'application/octet-stream', true, null, 'v2');
        // 判断是否上传正确
        if ( $err !== null ) return false;
        if ( $resp['key'] ) return "http://qiniu.yantianzz.com/{$resp['key']}";
    }

    // 获取七牛上传token
    public function bucket(){
        if ( $err = $this->actionAuth() ) return $this->setError( $this->filed[$err] ,$err);
        $bucket = 'ytydh';
        // 构建鉴权对象
        $auth = new Auth($this->AK,$this->SK);
        // 返回token
        $token = $auth->uploadToken($bucket);
        //
        return $this->toJson(['data'=>$token,'host'=>env('conf.qiniu.host'),'region'=>'ECN']);
    }
}
