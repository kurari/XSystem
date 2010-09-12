<?php
require_once dirname(__FILE__).'/../script/bin.header.inc.php';

$target = $argv[1];
$pos = strrpos(basename($target), '.');
$class = ucfirst(substr(basename($target), 0, $pos)).'Test';
require_once $target;

try {
	$TestCase = new $class( );
	$TestCase->init( );
	$TestCase->run( );
}catch(Exception $e){
	echo $e->getMessage();
	echo $e->getFile();
	echo $e->getLine();
}
?>
