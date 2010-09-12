<?php
require_once dirname(__FILE__).'/../script/bin.header.inc.php';
require_once 'test/runner.class.php';

$all = array_slice($argv, 1);
$Runner = new XTestRunner( );
foreach($all as $t ) $Runner->addCase( $t );
$Runner->run( );
?>
