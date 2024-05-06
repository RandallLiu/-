
<!-- Main navbar -->
<div class="navbar navbar-default header-highlight navbar-fixed-top" style="border-width:0;background-color: #fbfafa;">
    <div class="navbar-header">
        <div class="navbar-brand text-bold" style="color: #fff">
            <?=APPNAME?>
        </div>
        <ul class="nav navbar-nav visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
            <li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
        </ul>
    </div>

    <div class="navbar-collapse collapse" id="navbar-mobile">
        <div class="navbar-text">
           <div class="text-muted">
               <?php
               $GET_URI = new \CodeIgniter\HTTP\URI(current_url(true));
               $URI = substr($GET_URI->getPath(),1);
               ?>
            </div>
        </div>
        <ul class="nav navbar-nav">
            <li class="<?=(in_array($URI,["colleges/index","/",""]))?"active":"" ?>"><a href="/colleges/index" style="font-size: 16px">选学校</a></li>
            <li class="<?=($URI=="special/choose")?"active":"" ?>"><a href="/special/choose" style="font-size: 16px">选专业</a></li>
            <li class="<?=($URI=="monzy")?"active":"" ?>"><a href="/monzy" style="font-size: 16px">模拟预测</a></li>
            <li class="<?=($URI=="special/index")?"active":"" ?>"><a href="/special/index" style="font-size: 16px">专业库</a></li>
            <li class="<?=($URI=="score")?"active":"" ?>"><a href="/score" style="font-size: 16px">批次线</a></li>
            <li class="<?=($URI=="section")?"active":"" ?>"><a href="/section" style="font-size: 16px">一分一段</a></li>
            <li><a href="#" style="font-size: 16px">高考咨询</a></li>
        </ul>

        <ul class="nav navbar-nav navbar-right" >

            <li  class="mr-10">
               <a href="/v2/kemu/selected" class="aprovince hModal" data-text="保存" lang="填写高考信息">
                   <?php $kemu = session('kemu'); ?>
                   <?=($kemu?(array_key_exists('province_name',$kemu) ? $kemu['province_name'] : \App\Libraries\LibComm::$province[$kemu['province']]):"北京")?>
                   <?=($kemu?("· ".($kemu['type_name']?:(array_key_exists("typeId",$kemu)?\App\Libraries\LibComm::$kemu[$kemu['typeId']]:''))):"")?>

                   <?php if ( in_array($kemu['typeId'],['3','2073','2074'])):?>
                   (
                    <?php foreach ( $kemu['kemu'] as $km ):?>
                           <?=mb_substr(\App\Libraries\LibComm::$ksxk[$km],0,1)?>
                    <?php endforeach;?>
                   )
                   <?php endif;?>
                   <?=($kemu?("· ".$kemu['score']):"")?>
               </a>
            </li>

            <?php if (session("name")) :?>
            <li class="dropdown dropdown-user">
                <a class="dropdown-toggle" data-toggle="dropdown">
                    <i class="icon-user"></i>
                    <span> <?=session('name')?> </span>
                    <i class="caret"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-right">
                    <li><a href="/mycard" ><i class="icon-file-check"></i> 我的志愿卡 </a></li>
                    <li><a href="/admin/users/passwd" class="hModal"><i class="icon-lock2"></i> 重置密码 </a></li>
                    <li><a href="/logout"><i class="icon-switch2"></i> 退出</a></li>
                </ul>
            </li>
            <?php else: ?>
            <li>
                <a class="hModal btn-rounded" href="/v2/comm/login" data-text="登录" style="display:inline-block;" id="alogin">登录</a>
                <a class="hModal btn-rounded" href="/v2/comm/reg" style="display:inline-block;">注册</a>
            </li>
            <?php endif;?>
        </ul>
    </div>
</div>