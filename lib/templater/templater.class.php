<?php
/**
 * Templater
 *
 */

/**
 * store object
 */
require_once 'store/store.class.php';

/**
 * Exception
 */
require_once 'base/exception.class.php';
require_once 'base/base.class.php';
class XTemplaterException extends XBaseException{ }
class XTemplaterDisplayException extends XBaseException{ 
	public $orign;
	public $code;

	function __construct($e, $code)
	{
		$this->origin = $e;
		$this->code = $code;
	}

	function getCompiledCode( )
	{
		return $code;
	}

	function getOrign( )
	{
		return $this->origin;
	}

}

/**
 * Resource handler
 */
require_once 'templater/resource.class.php';

/**
 * Compile Logic
 */
require_once 'templater/templaterCompile.class.php';
require_once 'templater/compiler.class.php';
require_once 'templater/resource.class.php';
class XTemplater extends XBase
{
	/**
	 * left delimiter
	 * @var string
	 */
	protected $leftDelimiter  = '{{';

	/**
	 * right delimiter
	 * @var string
	 */
	protected $rightDelimiter = '}}';

	/**
	 * plugin directory
	 * @var string 
	 */
	protected $pluginDir = "";

	/**
	 * regexp
	 * @var array
	 */
	protected $quick = array(
		'resource'=>'([^:]+)://(.*)'
	);

	/**
	 * plugins 
	 * @var array
	 */
	public $plugin = array(
		'resource'=>array( ),
		'compiler'=>array( )
	);

	/**
	 * template vars
	 * @var array
	 */
	protected $_template_vars = array( );

	/**
	 * compiled code
	 * @var string
	 */
	protected $_last_code = false;

	/**
	 * cache controller
	 */
	public $Cache;

	function __construct( )
	{
		$this->pluginDir = array(
			dirname(__FILE__).'/plugin'
		);
		$this->cacheDir = "/tmp";

	}

	function getCacheCtrl( )
	{
		if( empty($this->Cache) ) {
			require_once 'kvs/kvs.class.php';
			$dir = $this->cacheDir;
			$this->Cache = XKeyValueStore::factory('sqlite', array('path'=>$dir."/cache.db"));
		}
		return $this->Cache;
	}

	/**
	 * Get Compiled Last Code
	 *
	 * @return string last compiled code
	 */
	public function getLastCode( )
	{
		return $this->_last_code;
	}

	/**
	 * Get Delimiter
	 *
	 * @return array {leftDelimiter,rightDelimiter}
	 */
	public function getDelimiter( )
	{
		return array( $this->leftDelimiter, $this->rightDelimiter );
	}

	/**
	 * register resource handler
	 *
	 * @param string name
	 * @param callback get
	 * @param callback getExpiredTime
	 * @param callback hasCache
	 * @param callback putCache
	 * @param callback getCachs
	 * @param callback gcCachs
	 */
	public function registerResourceHandler( $type, $get, $expired, $hasCache, $putCache, $getCache, $gcCache ) 
	{
		return $this->plugin['resource'][$type] = XTemplaterResource::factory( $get, $expired, $hasCache, $putCache, $getCache, $gcCache );
	}

	/**
	 * register compiler handler
	 *
	 * @param string name
	 * @param callback compile
	 */
	public function registerCompilerHandler( $type, $compile ) 
	{
		return $this->plugin['compiler'][$type] = XTemplaterCompiler::factory( $compile );
	}

	/**
	 * load compiler
	 */
	function loadCompilerHandler( ){
		foreach($this->pluginDir as $d ){
			foreach(glob("$d/compiler.*.plugin.class.php") as $file){
				require_once $file;
				$arr = explode('.',basename($file),3);
				$type = $arr[1];
				$class = 'XTemplaterCompiler'.ucfirst($type);
				if(!class_exists($class)) throw new XTemplaterCompilerException('compiler %s is not defined', $type);

				$o = new $class( $this );
				$this->registerCompilerHandler( $type, array($o,'compile') );
			}
		}
	}

	/**
	 * gettin compile handler
	 *
	 * @param string name
	 */
	function getCompilerHandler( $type )
	{
		if( isset($this->plugin['compiler'][$type]) ) {
			return $this->plugin['compiler'][$type];
		}
	}
	/**
	 * gettin resource handler
	 *
	 * @param string name
	 */
	function getResourceHandler( $type )
	{
		// if alrady loaded handler exists
		if( isset($this->plugin['resource'][$type]) ) {
			return $this->plugin['resource'][$type];
		}

		// info
		$fileName = "resource.$type.plugin.class.php";
		$class    = "XTemplaterResource".ucfirst($type);
		$file     = false;

		// search in plugin directorys 
		foreach($this->pluginDir as $d ) {
			$file = XUtil::makePath($d,$fileName);
			if(file_exists($file)) break;
		}

		// if not found
		if( $file == false ) return false;

		require_once $file;

		// if class not found
		if(!class_exists($class)) return false;

		// create instance
		$o = new $class($this);

		// save as resource handler
		return $this->registerResourceHandler( 
			$type,
			array($o,'get'),
			array($o,'getExpiredTime'),
			array($o,'hasCache'),
			array($o,'getCache'),
			array($o,'putCache'),
			array($o,'gcCache')
		);
	}


	/**
	 * resource getter
	 *
	 * @param string path file:///home/kurari/tpl.html
	 *                    for mat is  <type>://<path>
	 */
	function getResource( $path ) {
		// split resource type and path
		$res = explode( '://', $path, 2);

		// if it was not correct
		if(empty($res[1])) throw new XTemplaterException('Resource path incorrect (%s)',$path);

		$type = $res[0];
		$path = $res[1];

		if(false == $handler = $this->getResourceHandler( $type )) throw new XTemplaterException('Resource type %s is not registerd',$type);

		return $handler->doGet($path, $this);
	}

	/**
	 * compile
	 */
	function compile( $data, $path = false )
	{
		$C = new XTemplaterComplie( $this, $path );
		$C->compile( $data );
		return $C->fetch( );
	}

	/**
	 * display
	 */
	function display( $resource, $additional_vars= array() )
	{
		// split resource type and path
		$res = explode( '://', $resource, 2);

		// if it was not correct
		if(empty($res[1])) throw new XTemplaterException('Resource path incorrect (%s)',$resource);

		$type = $res[0];
		$path = $res[1];

		if(false == $handler = $this->getResourceHandler( $type )) throw new XTemplaterException('Resource type %s is not registerd',$type);

		// GC
		$CC = $this->getCacheCtrl( );
		$CC->gc($handler->getExpiredTime($path,$this), $resource);

		if(false === $code = $CC->get( $resource ) ){
			$text    = $handler->doGet( $path, $this );
			$expired = $handler->getExpiredTime( $path, $this );
			$code    = $this->compile( $text, $resource );
			$CC->set( $resource, $code );
		}
		$this->_last_code = $code;

		$store = XStore::factory('array', $this->_template_vars);
		$store->set( $additional_vars );

		try {
			eval('?> '.$code);
		}catch(Exception $e) {
			throw new XTemplaterDisplayException( $e, $code );
		}
	}


	/**
	 * assing vars
	 */
	function assign( $key, $value )
	{
		if(is_array($key)){
			foreach( $key as $k=>$v) $this->assign($k, $v);
			return false;
		}
		$this->_template_vars[$key] = $value;
	}

	function getOpt( $text ){
		$text = $this->compileVar( $text );
		if( preg_match_all('/\s*([^=]+)\s*=\s*( (?:[^"\'(\s]|  "[^"]+" | \([^)]*\) |  \'[^\']+\')+ )\s*/xms',$text,$m) ){
			$opt = array_combine( $m[1], $m[2] );
		}
		$store = XStore::factory('array', $opt);
		return $store;
	}

	/**
	 * Utility
	 */
	function compileVar( $text ) {
		$text = preg_replace('/\$([a-zA-Z0-9_.]+)/', '$store->get("\1")', $text);
		return $text;
	}
}
?>
