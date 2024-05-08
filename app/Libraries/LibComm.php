<?php
namespace App\Libraries;

use App\Models\Admin\Users;

class LibComm {

    /**
     * 图形验证码
     *
     * */
    static function Captcha(){
        // session_start();
        $session = \CodeIgniter\Config\Services::session();
        //设置需要随机取的值,去掉容易出错的值如0和o
        $data ='abcdefghigkmnpqrstuvwxy123456789ABCDEFGHIJKLMNPQRSTUVWSYZ';
        $image = imagecreatetruecolor(100, 35);    //1>设置验证码图片大小的函数
        //5>设置验证码颜色 imagecolorallocate(int im, int red, int green, int blue);
        $bgcolor = imagecolorallocate($image,255,255,255); //#ffffff
        //6>区域填充 int imagefill(int im, int x, int y, int col) (x,y) 所在的区域着色,col 表示欲涂上的颜色
        imagefill($image, 0, 0, $bgcolor);
        //10>设置变量
        $captcha_code = "";
        function gen_char($data) {
            $char = substr($data, rand(0,strlen($data) - 1),1);
            return $char?:gen_char($data);
        }

        //7>生成随机的字母和数字
        for($i=0;$i<4;$i++){
            //设置字体大小
            $fontsize = 18;
            //设置字体颜色，随机颜色
            $fontcolor = imagecolorallocate($image, rand(0,120),rand(0,120), rand(0,120));      //0-120深颜色
            //取出值，字符串截取方法  strlen获取字符串长度
            $fontcontent = gen_char($data);
            //10>.=连续定义变量
            $captcha_code .= $fontcontent;
            //设置坐标
            $x = ($i*100/4)+rand(5,10);
            $y = rand(5,10);
            imagestring($image,$fontsize,$x,$y,$fontcontent,$fontcolor);
        }
        //10>存到session
        // $_SESSION['authcode'] = $captcha_code;
        $session->set("authcode",$captcha_code);
        //8>增加干扰元素，设置雪花点
        for($i=0;$i<200;$i++){
            //设置点的颜色，50-200颜色比数字浅，不干扰阅读
            $pointcolor = imagecolorallocate($image,rand(50,200), rand(50,200), rand(50,200));
            //imagesetpixel — 画一个单一像素
            imagesetpixel($image, rand(1,99), rand(1,29), $pointcolor);
        }
        //9>增加干扰元素，设置横线
        for($i=0;$i<3;$i++){
            //设置线的颜色
            $linecolor = imagecolorallocate($image,rand(80,220), rand(80,220),rand(80,220));
            //设置线，两点一线
            imageline($image,rand(1,99), rand(1,29),rand(1,99), rand(1,29),$linecolor);
        }

        //2>设置头部，image/png
        header('Content-Type: image/png');
        //3>imagepng() 建立png图形函数
        imagepng($image);
        //4>imagedestroy() 结束图形函数 销毁$image
        imagedestroy($image);
    }

    /**
     *  数据导入
     * @param string $file excel文件
     * @param string $sheet
     * @return string   返回解析数据
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    static function ReadExcel($file='', $sheet = 0) {
        //引用PHPexcel 类
        require(APPPATH.'Libraries/PHPExcel.php');
        require(APPPATH.'Libraries/PHPExcel/IOFactory.php');//静态类

        $file = iconv("utf-8", "gb2312", $file);   //转码

        if(empty($file) OR !file_exists($file)) {
            die('file not exists!');
        }
        $objRead = new \PHPExcel_Reader_Excel2007();   //建立reader对象
        if(!$objRead->canRead($file)){
            $objRead = new \PHPExcel_Reader_Excel5();
            if(!$objRead->canRead($file)){
                die('No Excel!');
            }
        }
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ');
        $obj = $objRead->load($file);  //建立excel对象
        $currSheet = $obj->getSheet($sheet);   //获取指定的sheet表
        $columnH = $currSheet->getHighestColumn();   //取得最大的列号
        $columnCnt = array_search($columnH, $cellName);
        $rowCnt = $currSheet->getHighestRow();   //获取总行数
        $data = [];
        for($_row=1; $_row<=$rowCnt; $_row++){  //读取内容
            if($_row > 1) {
                for ($_column = 0; $_column <= $columnCnt; $_column++) {
                    $cellId = $cellName[$_column] . $_row;
                    $cellValue = $currSheet->getCell($cellId)->getValue();
                    if ($cellValue instanceof \PHPExcel_RichText) {   //富文本转换字符串
                        $cellValue = $cellValue->__toString();
                    }

                    $data[$_row - 2][$cellName[$_column]] = $cellValue;
                }
            }
        }
        return $data;
    }

    /**
     * 数据导出
     * @param array $title   标题行名称
     * @param array $data   导出数据
     * @param string $fileName 文件名
     * @param string $savePath 保存路径
     * @param $type   是否下载  false--保存   true--下载
     * @return string   返回文件全路径
     * @throws \PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    static function ExportExcel($title=array(), $data=array(), $fileName='', $savePath='./', $isDown=true){

        require_once (APPPATH.'Libraries/PHPExcel.php');
        require_once (APPPATH.'Libraries/PHPExcel/IOFactory.php');//静态类
        $obj = new \PHPExcel();
        //横向单元格标识
        $cellName = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ','BA','BB','BC','BD','BE','BF','BG','BH','BI','BJ','BK','BL','BM','BN','BO','BP','BQ','BR','BS');
$obj->getActiveSheet(0)->setTitle(date('YmdHis'));   //设置sheet名称
        $_row = 1;   //设置纵向单元格标识

        if($title){
            $i = 0;
            foreach($title AS $v){   //设置列标题
                $obj->setActiveSheetIndex(0)->setCellValue($cellName[$i].$_row, $v);
                $i++;
            }
            $_row++;
        }

        //填写数据
        if($data){
            $i = 0;
            foreach($data AS $_v){
                $j = 0;
                foreach($_v AS $_cell){
                    $obj->getActiveSheet(0)->setCellValue($cellName[$j] . ($i+$_row), $_cell);
                    $j++;
                }
                $i++;
            }
        }

        //文件名处理
        if(!$fileName){
            $fileName = uniqid(time(),true);
        }

        //$objWrite = \IOFactory::createWriter($obj, 'Excel2007');
        $objWrite = \IOFactory::createWriter($obj, 'Excel5');

        if($isDown){   //网页下载
            header('pragma:public');
            header("Content-Disposition:attachment;filename=$fileName.xls");
            $objWrite->save('php://output');exit;
        }

        $_fileName = iconv("utf-8", "gb2312", $fileName);   //转码
        $_savePath = $savePath.$_fileName.'.xlsx';
        $objWrite->save($_savePath);

        return $savePath.$fileName.'.xlsx';
    }

    /**
     * object to array
     * @param array $object   标题行名称
     * @return array   返回文件全路径
     */
    static function obj2array(&$object) {
        $object =  json_decode( json_encode( $object),true);
        return  $object;
    }

    /**
     * 将pdf转化为单一png图片
     *
     * @param string $pdf  pdf所在路径 （/www/pdf/abc.pdf pdf所在的绝对路径）
     * @param string $path 新生成图片所在路径 (/www/pngs/)
     * */
    static function pdf_to_png( $pdf , $path,$name = null ){
        try {
            if(!extension_loaded('imagick')){
                log_message('error','not unload imagick');
            }
            log_message('error',$pdf);
            if(!file_exists($pdf)){
                log_message('error','the path of pdf is not exists!');
            }

            $im = new \Imagick();
            $im->setCompressionQuality(100);
            $im->setResolution(120, 120);//设置分辨率 值越大分辨率越高
            log_message('error','Readding');
            $im->readImage($pdf);

            $canvas = new \Imagick();
            $imgNum = $im->getNumberImages();
            $paddingwidth = 25;
            $paddingheight = 30;
            foreach ($im as $k => $sub) {
                $sub->setImageFormat('png');
                //$sub->setResolution(120, 120);
                $sub->stripImage();
                $sub->trimImage(0);
                $width = $sub->getImageWidth() + 2 * $paddingwidth;
                $height = $sub->getImageHeight() + 2 * $paddingheight;
                if ($k + 1 == $imgNum) {
                    $height += $paddingheight;
                } //最后添加10的height
                $canvas->newImage($width, $height, new \ImagickPixel('white'));

                $canvas->compositeImage($sub, \Imagick::COMPOSITE_DEFAULT, $paddingwidth, $paddingheight);
            }

            $canvas->resetIterator();
            if( $name ){
                $newfile_name = $path . $name . '.png';
            }else{
                $newfile_name = $path . microtime(true) . '.png';
            }
            $canvas->appendImages(true)->writeImage($newfile_name);

            return $newfile_name;
        } catch (\Exception $e) {
            log_message('error',$e->getMessage());
            throw $e;
        }
    }

    static $ksxk = ["2" => "物理","5" => "历史","3"=>"化学","4"=>"生物","6"=>"政治","7"=>"地理"];

    static $batch = [
        "10"=>"专科批",
        "11"=>	"专科提前批",
        "111"=>	"本科一批I段",
        "113"	=>"本科二批K段",
        "1137"=>	"高校专项计划本科批",
        "119"	=>"高职（专科）批R段",
        "12"	=>"国家专项计划本科批",
        "13"	=>"地方专项计划本科批",
        "14"	=>"本科批",
        "15"	=>"普通类提前批",
        "1534"=>	"本科二批及预科",
        "1561"=>	"艺术类本科批",
        "1564"=>	"本科一段",
        "1565"=>	"本科二段",
        "1570"=>	"普通类一段",
        "1571"=>	"普通类二段",
        "16"	=>"平行录取一段",
        "17"	=>"平行录取二段",
        "1939"=>	"普通类平行录取",
        "1989"=>	"提前批",
        "2583"=>	"艺术本科A段",
        "2642"=>	"本科第一批",
        "2667"=>	"艺术本科批A段",
        "2668"=>	"艺术本科批B段",
        "2669"=>	"艺术本科批C段",
        "2674"=>	"本科第二批",
        "2685"=>	"本科一批U段",
        "2699"=>	"艺术本科提前批B段",
        "2726"=>	"体育类本科批",
        "2734"=>	"体育类第一段",
        "2741"=>	"艺术类第二批",
        "2777"=>	"艺术类本科A段",
        "2794"=>	"提前一批本科",
        "2795"=>	"提前二批本科",
        "2915"=>	"艺术类第一批A段",
        "2929"=>	"艺术本科二批",
        "2935"=>	"第二批",
        "2949"=>	"本科一批T段",
        "3373"=>	"本科第二批提前批",
        "3374"=>	"本科第一批提前批", "3375"=>	"提前批第一批本科", "3379"=>	"提前批第二批本科", "3380"=>	"提前批第二批专科", "36"	=>"本科提前批A段", "37"	=>"本科提前批B段", "44"	=>"本科二批A段", "45"	=>"本科二批B段", "46"	=>"本科批A段", "47"	=>"本科批B段", "48"	=>"专科批A段", "51"	=>"本科一批A段", "52"	=>"本科一批B段", "54"	=>"本科二批C段", "6"	=>"本科提前批", "69"	=>"本科综合评价批", "7"	=>"本科一批", "8"	=>"本科二批", "86"	=>"本科提前批C段", "90"	=>"特殊类型招生批", "98"	=>"本科一批A1段"];

    static $kemu = ["1"=> "理科", "2"=> "文科", "3"=> "综合", "4"=> "艺术类", "5"=> "体育类", "23"=> "体育文", "24"=> "体育理", "25"=> "艺术文", "26"=> "艺术理", "2073"=> "物理类", "2074"=> "历史类"];

    // static $kemu2 = ["0"=> "理科", "1"=> "文科", "3"=> "综合", "4"=> "艺术类", "5"=> "体育类", "23"=> "体育文", "24"=> "体育理", "25"=> "艺术文", "26"=> "艺术理", "2073"=> "物理类", "2074"=> "历史类"];

    static $province = ["11"=>"北京", "12"=>"天津","31"=>"上海", "50"=>"重庆", "13"=>"河北", "41"=>"河南", "53"=>"云南", "21"=>"辽宁", "23"=>"黑龙江", "43"=>"湖南", "34"=>"安徽", "37"=>"山东", "65"=>"新疆", "32"=>"江苏", "33"=>"浙江", "36"=>"江西", "42"=>"湖北", "45"=>"广西", "62"=>"甘肃", "14"=>"山西", "15"=>"内蒙古", "61"=>"陕西", "22"=>"吉林", "35"=>"福建", "52"=>"贵州", "44"=>"广东", "63"=>"青海", "54"=>"西藏", "51"=>"四川", "64"=>"宁夏", "46"=>"海南"];

    //
    static $change_kemu = [
        '23' => ['1' => '2073','2' => '2074'],  // 黑龙江
        '34' => ['1' => '2073','2' => '2074'],  // 安徽
        '36' => ['1' => '2073','2' => '2074'],  // 江西
        '45' => ['1' => '2073','2' => '2074'],  // 广西
        '62' => ['1' => '2073','2' => '2074'],  // 甘肃
        '22' => ['1' => '2073','2' => '2074'],  // 吉林
        '52' => ['1' => '2073','2' => '2074'],  // 贵州
    ];

    // 本科批次
    static $batch_bk = ['111','113','14','1564','1565','2642','2674','44','45','46','47','48','51','52','54','7','8'];
    // 专科批次
    static $batch_zk = ['48','49','10'];
}
