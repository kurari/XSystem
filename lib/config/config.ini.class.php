<?php
/**
 * XConf
 * -----------------------------
 * Type : ini
 * -----------------------------
 */

class XConfigIni extends XConfig
{

	public function load( $file )
	{
		try {
			$fp = fopen($file,'r');
		}catch(Exception $e){
			throw XConfigException("file error %s", $e->getMessage());
		}

		$Root = new XConfigContainer( );
		$C = $Root;

		while( $line = fgets( $fp, 1024) ) {

			// For Comment line
			if( $line{0} == ";" ) continue;

			$line = trim($line);
			if( empty($line) )  continue;

			// For Section
			if( $line{0} == '['  && $line[strlen($line)-1] == ']' ) {
				$C = $Root->createSection( substr($line,1,strlen($line)-2) );
			}

			// Key Value 
			if( false !== $pos = strpos($line, '=') ) {
				$key = trim( substr($line, 0, $pos ) );
				$key = preg_replace('/\[(.*)\]/','.\1', $key);
				$value = trim( substr($line, $pos+1) );
				$C->setValue( $key, $value);
			}
		}
		fclose($fp);

		return $Root;
	}
}

class XConfigContainer 
{
	var $children = array();
	var $section = array();
	var $values = array( );

	function createSection( $name ) {
		$this->section[$name] = new XConfigContainer( );
		return $this->section[$name];
	}

	function setValue( $key, $value ) {
		if( false === strpos($key,'.') ){
			return $this->values[$key] = $value;
		}
		$arr = explode('.',$key);
		$fin = array_pop($arr);
		$ref =& $this->values;
		while( $k = array_shift($arr) ) $ref =& $ref[$k];
		$ref[$fin] = $value;
	}

	function getValue( $key ) {
		if( false === strpos($key,'.') ){
			return $this->values[$key];
		}
		$arr = explode('.',$key);
		$fin = array_pop($arr);
		$ref = $this->values;
		while( $k = array_shift($arr) ) $ref = $ref[$k];
		return $ref[$fin];
	}


	function toArray( $section = false) {
		if( false === $section ) {
			$ret = $this->values;
			foreach($this->section as $sec=>$v ){
				$ret[$sec] = $array = $this->toArray($sec);
			}
			return $ret;
		}
		return $this->section[$section]->values;
	}
}
?>
