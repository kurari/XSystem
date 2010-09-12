<?php
define('XSCRIPT',dirname(__FILE__));
define('XROOT', realpath(dirname(__FILE__).'/../'));
require_once XSCRIPT.'/function.inc.php';
xAddIncludePath(xMakePath(XROOT,'class'), xMakePath(XROOT,'lib'));

spl_autoload_register('xAutoLoad');


// Error to Exception
function exception_error_handler($errno, $errstr, $errfile, $errline ) {
	    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}
set_error_handler("exception_error_handler");
?>
