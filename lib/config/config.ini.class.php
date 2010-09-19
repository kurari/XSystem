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

		while( $line = fgets( $fp, 1024) ) {

			// For Comment line
			if( $line{0} == ";" ) continue;

			$line = trim($line);
			if( empty($line) )  continue;

			// For Section
			if( $line{0} == '['  && $line[strlen($line)-1] == ']' ) {
				$this->setSection( substr($line,1,strlen($line)-2) );
			}

			// Key Value 
			if( false !== $pos = strpos($line, '=') ) {
				$key = trim( substr($line, 0, $pos ) );
				$key = preg_replace('/\[(.*)\]/','.\1', $key);
				$value = trim( substr($line, $pos+1) );
				switch($value){
				case "1": $value = true; break;
				case "0": $value = false; break;
				}
				$this->set( $key, $value);
			}
		}
		fclose($fp);
		$this->setSection( false );
	}

	public function outputFilter( $key, $value ){
		return parent::outputFilter($key, $value );
	}
}

?>
