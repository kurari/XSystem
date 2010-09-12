<?php
function xMakePath( ){
	$args = func_get_args( );
	return implode('/', $args);
}
function xAddIncludePath( ){
	$args = func_get_args( );
	$args[] = get_include_path( );
	set_include_path(implode(PATH_SEPARATOR, $args));
}
function xAutoLoad( $name ) {
	if(preg_match('/X([A-Z][a-z]+)([A-Z][a-z]+)/', $name, $m)){
		$dir = strtolower($m[1]);
		$name = strtolower($m[2]).'.class.php';
		require_once xMakePath($dir,$name);
		return true;
	}elseif(preg_match('/X(.*)/', $name, $m)){
		$file = strtolower($m[1]).'.class.php';
		require_once $file;
	}
}
?>
