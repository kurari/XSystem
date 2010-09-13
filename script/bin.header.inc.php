<?php
define('XROOT', realpath(dirname(__FILE__).'/../'));
require_once XROOT.'/lib/util/util.class.php';

XUtil::addIncludePath(
	XUtil::makePath(XROOT,'class'), 
	XUtil::makePath(XROOT,'lib')
);

spl_autoload_register(
	array('XUtil','autoLoad')
);

// Error to Exception
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
	    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");
?>
