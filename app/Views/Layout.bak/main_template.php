<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="keywords" content="">
    <?= $this->include('/Layout/assets')?>
    <script src="/dist/js/jquery.3.7.min.js"></script>
</head>
<body>

<div class="page">
    <header class="navbar navbar-expand-md d-print-none" style="background-color: #f6f8fb">
        <div class="container-xl">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <h1 class="navbar-brand navbar-brand-autodark d-none-navbar-horizontal pe-0 pe-md-8">
                <a href=".">
                    <img src="/dist/img/logo.small.svg" width="110" height="32" alt="好志愿填报" class="navbar-brand-image">
                </a>
            </h1>

            <div class="collapse navbar-collapse ml-3" id="navbar-menu">
                <div style="width: 60%">
                    <form action="./" method="get" autocomplete="off" novalidate="">
                        <div class="input-icon">
                          <span class="input-icon-addon">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0"></path><path d="M21 21l-6 -6"></path></svg>
                          </span>
                            <input type="text" value="" class="form-control" placeholder="搜索学校,专业">
                        </div>
                    </form>
                </div>
            </div>

            <div class="navbar-nav flex-row order-md-last">

                <div class="nav-item d-none d-md-flex me-3">
                    <div class="btn-list">
                        <a href="#" class="btn btn-azure btn-pill btn-sm" target="_blank" rel="noreferrer">
                            登录
                        </a>
                        <a href="#" class="btn btn-warning btn-pill btn-sm" target="_blank" rel="noreferrer">
                            注册
                        </a>
                    </div>
                </div>

                <div class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown" aria-label="Open user menu">
                        <span class="bg-muted-lt">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 5h2m4 0h12"></path><path d="M3 19h16"></path><path d="M4 9l2 6h1l2 -6"></path><path d="M12 12v3"></path><path d="M16 12v-3h2a2 2 0 1 1 0 4h-1"></path><path d="M3 3l18 18"></path></svg>
                        </span>

                        <span class="bg-yellow text-yellow-fg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 5h18"></path><path d="M3 19h18"></path><path d="M4 9l2 6h1l2 -6"></path><path d="M12 9v6"></path><path d="M16 15v-6h2a2 2 0 1 1 0 4h-2"></path></svg>
                        </span>

                        <div class="d-none d-xl-block ps-2">
                            1381
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <header class="navbar-expand-md">
        <div class="collapse navbar-collapse" id="navbar-menu">
            <div class="navbar">
                <div class="container-xl">
                    <ul class="navbar-nav">
                        <li class="nav-item active">
                            <a class="nav-link" href="/" >
                                <span class="nav-link-title">
                                  首页
                                </span>
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="/">
                                <span class="nav-link-title">
                                  查学校
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="./" >
                                <span class="nav-link-title">
                                  查专业
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span class="nav-link-title">
                                  位次
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" >
                                <span class="nav-link-title">
                                  批次线
                                </span>
                            </a>
                        </li>
                    </ul>

                    <!--div class="my-2 my-md-0 flex-grow-1 flex-md-grow-0 order-first order-md-last">
                        <form action="/" method="get" autocomplete="off" novalidate>
                            <div class="input-icon">
                                <span class="input-icon-addon">
                                  <svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" /><path d="M21 21l-6 -6" /></svg>
                                </span>
                                <input type="text" value="" class="form-control" placeholder="查义学校,专业" aria-label="Search in website">
                            </div>
                        </form>
                    </div-->
                </div>
            </div>
        </div>
    </header>
    <div class="page-wrapper">
        <!-- Page body -->
        <div class="page-body">
            <div class="container-xl">
                <?=$this->renderSection('content')?>
            </div>
        </div>

        <!-- footer -->
        <div class="footer">
            <div class="footbody">
                <div class="footer-list">
                    <div class="footer-content">
                        <ul class="one_ul clr">
                            <li class="item">
                                <dl>
                                    <dt>高校查询</dt>
                                    <dd><a href="/schools.html" target="_blank">大学介绍</a></dd>
                                    <dd><a href="/schools.html" target="_blank">开设专业</a></dd>
                                    <dd><a href="/schools.html" target="_blank">重点专业</a></dd>
                                </dl>
                            </li>
                            <li class="item">
                                <dl>
                                    <dt>录取数据</dt>
                                    <dd><a href="/index/school-score.html" target="_blank">录取分数</a></dd>
                                    <dd><a href="/index/major-score.html" target="_blank">专业分数</a></dd>
                                    <dd><a href="/index/school-plan.html" target="_blank">招生计划</a></dd>
                                </dl>
                            </li>

                            <li class="item">
                                <dl>
                                    <dt>志愿填报</dt>
                                    <dd><a href="/cesuan">智能填报</a></dd>
                                    <dd><a href="/yifenyiduan.html" target="_blank">一分一段</a></dd>
                                    <dd><a href="/daxuepaiming.html" target="_blank">大学排名</a></dd>
                                </dl>
                            </li>
                            <li class="item" style="border: none;">
                                <dl class="last">
                                    <dt>关注联系我们</dt>
                                    <dd><a href="javascript:void(0)" class="on"><div class="img" style="display: block;"><img src="/dist/img/qrcode.png" style="height: 82px;"></div></a></dd>
                                </dl>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="content12">
                    <div class="footer-ba">
                        <span>Copyright © 2023 好志愿版权所有 All Rights Reserved</span>
                        <span style="margin-left: 20px;">地址：苏州市高新园区X栋S号 </span>
                        <div style="padding-top: 10px;">工信部备案号：<a href="https://beian.miit.gov.cn/" target="_blank" class="hcainfo_num">苏ICP备XXXXXXXX号-1</a>
                            <!--div style="padding-top: 10px;">出版物经营许可证：
                                <a href="http://www.juzhiyuan.com/2.html" target="_blank" class="hcainfo_num">新发出包河字第2022053号</a>
                            </div-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end footer -->
    </div>
</div>
<!-- Tabler Core -->
<script src="/dist/js/tabler.min.js" defer></script>
<script src="/dist/js/demo.min.js" defer></script>
</body>
</html>