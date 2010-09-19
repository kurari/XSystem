<?php
/**
 * XSystem FrameWork
 * -----
 *
 */
require_once 'log/log.class.php';
require_once 'config/config.class.php';
require_once 'XMessage.class.php';

class XSystem  {

	public $Log;
	public $Conf;

	public static function factory( $conf, $option=array() ) {
		$Conf = XConfig::factory('ini');
		$Conf->load($conf);
		$Conf->set($option);

		$object = new XSystem( $Conf );
		return $object;
	}

	public function __construct( $Conf ) {
		$this->Conf = $Conf;
		$logConf = $Conf->get('log');
		$this->Log = XLog::factory($logConf['type'], $logConf['name'], $logConf['option']);
		$this->Log->info('XSystem Waik');
	}

	public function accept( $Message ) {
		// cascade to accepted message
		$type = $Message->get('type');
		$Res  = call_user_func(array($this,$type), $Message );
		return $Res;
	}

	public function display( $Message ){
		$file = $Message->get('app');
		$tpl  = $this->Conf->appDir."/$file.html";
		$View = new XSystemView($tpl);
		$View->assign("conf", $this->Conf->get());
		$View->run( );
		return new XMessage();
	}
}

require_once "templater/templater.class.php";
class XSystemView extends XTemplater
{
	public $tpl;

	public function __construct( $tpl )
	{
		parent::__construct( );
		$this->tpl = $tpl;
	}

	public function run( )
	{
		$this->display("file://".$this->tpl);
	}

}
?>
