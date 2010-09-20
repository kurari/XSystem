<?php
/**
 * XTemplater 
 * ---
 * Compile Logic
 */

class XTemplaterComplie {

	/**
	 * Compiling Buffer
	 */
	private $buf = "";

	/**
	 * Env Flag
	 */
	private $isEnv = false;

	/**
	 * counter
	 */
	private $cnt = 0;

	/**
	 * current env
	 */
	private $envName = "";

	/**
	 * current env
	 */
	private $envArg = "";

	/**
	 * output buffer
	 */
	private $output = "";

	/**
	 * compiler keys
	 */
	private $compilers = array();

	private $path;

	function __construct( $Tpl, $path )
	{
		$this->Tpl = $Tpl;
		$this->Tpl->loadCompilerHandler( );
		$this->path = $path;
		$this->compilers = array_keys( $this->Tpl->plugin['compiler'] );
	}

	public function fetch( )
	{
		return $this->output;
	}

	/**
	 * compile
	 */
	public function compile( $data )
	{
		list($l,$r) = $this->Tpl->getDelimiter( );
		$quick      = '/(?P<bef>.*?)(?P<tag>'.preg_quote($l) .'(?P<inner>.*?)'.preg_quote($r).')(?P<aft>.*)/xms';
		$text       = $data;
		while(preg_match($quick, $text, $m)){
			// next data
			$text = $m['aft'];
			// get tag info
			list($tagName,$tagArg) = $this->_getTagInfo($m['inner']);
			// if env tag
			if($this->isEnv == false){
				// out put 
				$this->_output($m['bef']);

				// if env start
				if( $this->_isBlock( $tagName, $m['inner'] ) ) {
					$this->_startEnv( $tagName, $tagArg );
					continue;
				}
				// for compiler
				if( $this->_isCompiler($tagName) ) {
					$this->_output( $this->_tagCompile( $tagName, $tagArg ) );
					continue;
				}
				if($tagName{0} == '$'){
					// for $hoge
					$compiler = $this->Tpl->getCompilerHandler('var');
					$this->_output( $compiler->doCompile("$tagName $tagArg", $tagName, "", $this->Tpl) );
					continue;
				}

				// nothing
				$this->_output($m['tag']);

			}else{
				// cnt up if same name of env open
				if($tagName == $this->envName && $m['inner']{0} != '/') $this->cnt++;
				if($tagName == "/".$this->envName) $this->cnt--;
				$this->buf .= $m['bef'];
				// if cnt is -1 close
				if( $this->cnt < 0 ){
					$this->_output( $this->_tagCompile( $this->envName, $this->envArg, $this->buf ) );
					$this->_endEnv( );
				}else{
					$this->buf .= $m['tag'];
				}
			}
		}
		$this->_output( $text );
	}

	private function _getTagInfo( $text )
	{
		$tagInfo = explode(" ", $text,2);
		$tagName = $tagInfo[0];
		$tagArg  = XUtil::arrayGetOr($tagInfo,1,"");

		return array($tagName, $tagArg );
	}

	private function _output( $text )
	{
		$this->output .= $text;
	}

	private function _isBlock( $tag, $contain )
	{
		return in_array($tag, $this->compilers) && $contain{0} !== "/" ? true: false;
	}

	private function _isCompiler( $tag )
	{
		return in_array($tag, $this->compilers);
	}

	private function _startEnv( $tag, $arg )
	{
		$this->envName = $tag;
		$this->envArg = $arg;
		$this->buf = "";
		$this->cnt = 0;
		$this->isEnv = true;
	}

	private function _endEnv( )
	{
		$this->buf = "";
		$this->cnt = 0;
		$this->isEnv = false;
	}

	private function _tagCompile( $tag, $arg, $buf = "" )
	{
		$compiler = $this->Tpl->getCompilerHandler($tag);
		return $compiler->doCompile( "$tag $arg", $arg, $buf, $this->Tpl, $this->path);
	}
}
?>
