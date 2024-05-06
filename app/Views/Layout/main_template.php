<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=APPNAME?></title>
    <?= $this->include('/Layout/assets')?>
    <style>
        .navbar-default .navbar-nav>.active>a, .navbar-default .navbar-nav>.active>a:hover, .navbar-default .navbar-nav>.active>a:focus {
            background-color: #f2f2f2;
        }
        .navbar-header{min-width:180px}
        .sidebar-fixed .sidebar-content,.sidebar {width: 180px;}
    </style>
</head>
<body>
<?= $this->include('/Layout/nav')?>
<!-- Page container -->
<div class="page-container">
    <!-- Page content -->
    <div class="page-content" >
        <!--div class="sidebar sidebar-main  sidebar-fixed">
            <div class="sidebar-content">
                <div class="sidebar-category sidebar-category-visible">
                    <div class="category-content no-padding">
                        <ul class="navigation navigation-main navigation-accordion sub_menu">
                            <?// =\App\Libraries\LibMenu::init_menu(); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div-->
        <!-- Main content -->
        <div class="content-wrapper" style="padding-top: 40px">
            <?=$this->renderSection('content')?>
        </div>
    </div>
</div>
    <div class="navbar-default navbar-sm  navbar-fixed-bottom" style="width: 100%;bottom: 0px">
        <div class="navbar-collapse collapse">
            <div class="navbar-text">
                © <?php echo date('Y')?>. <a href="#"><?php echo $_SERVER['HTTP_HOST']?></a> by <a href="http://<?php echo $_SERVER['HTTP_HOST']?>" target="_blank"></a>
            </div>
            <div class="navbar-right"></div>
        </div>
    </div>
</body>

</html>
