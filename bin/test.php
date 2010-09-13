<?php
require_once dirname(__FILE__).'/../script/bin.header.inc.php';
require_once 'test/runner.class.php';
ini_set('max_execution_time', 10);

try{
	$all = array_slice($argv, 1);
	$Runner = new XTestRunner( );
	foreach($all as $t ) $Runner->addCase( $t );
	$Runner->run( );
}catch(Exception $e){
	echo $e->getMessage();
}
?>
