<?php
require_once '../../lib/base/util.class.php';
XUtil::addIncludePath(
	XUtil::makePath("../..",'class'), 
	XUtil::makePath("../..",'lib')
);

require_once 'XSystem.class.php';
$file = "../etc/site.ini";

define("SITE_ROOT", realpath("../"));

$Mes = new XMessage( );
$Mes->set('type'     , 'display');
$Mes->set('app'      , 'bbs');
$Mes->set('function' , 'run');
$Mes->set('data'     , array());

$FW = XSystem::factory( $file, array('site.root' => SITE_ROOT));
$FW->accept($Mes);
?>
