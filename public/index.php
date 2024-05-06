<?php
//var_dump('OK');return;
// Valid PHP Version?
$minPHPVersion = '7.2';
if (phpversion() < $minPHPVersion)
{
	die("Your PHP version must be {$minPHPVersion} or higher to run CodeIgniter. Current version: " . phpversion());
}
unset($minPHPVersion);

define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
define('BASEURL',((( !isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] != "on" )?'http:':'https:') ) . '//'.$_SERVER['HTTP_HOST'].'/');
define('APPNAME','51高考志愿填报·内参版');
define('TOPSHOW',true);

$strPaths = '../%s/Config/Paths.php';

switch(strtolower($_SERVER['HTTP_HOST'])) {
    case 'dev.school.cn':
        $aPaths = sprintf($strPaths,'app');
        break;
    default:
        $aPaths = sprintf($strPaths,'app');
        break;
}
// Location of the Paths config file.
// This is the line that might need to be changed, depending on your folder structure.
$pathsPath = realpath(FCPATH . $aPaths);
// ^^^ Change this if you move your application folder

/*
 *---------------------------------------------------------------
 * BOOTSTRAP THE APPLICATION
 *---------------------------------------------------------------
 * This process sets up the path constants, loads and registers
 * our autoloader, along with Composer's, loads our constants
 * and fires up an environment-specific bootstrapping.
 */

// Ensure the current directory is pointing to the front controller's directory
chdir(__DIR__);
// Load our paths config file
require $pathsPath;
$paths = new Config\Paths();

// Location of the framework bootstrap file.
$app = require rtrim($paths->systemDirectory, '/ ') . '/bootstrap.php';


/*
 *---------------------------------------------------------------
 * LAUNCH THE APPLICATION
 *---------------------------------------------------------------
 * Now that everything is setup, it's time to actually fire
 * up the engines and make this app do its thang.
 */

//require_once '../vendor/autoload.php';

$app->run();
