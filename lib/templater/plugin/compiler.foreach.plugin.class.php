<?php
/**
 * XTemplater Plugin
 * ----------------------------------
 * Plugin : Compiler
 * Type   : foreach
 * ----------------------------------
 * 
 * @author Hajime 
 * @exception XTemplaterResourceException
 */

class XTemplaterCompilerForeach extends XTemplaterCompiler
{

	function compile( $tag, $args, $text, $Tpl )
	{
		$store = $this->getOpt($args);
		$store->setIf(array('value'=>'v', 'key'=>'k'));
		$output = "<?php \n";
		$output.= "\$from = $store->from; \n";
		$output.= 'if(count($from)>0) foreach( $from as $key=>$value ):'."\n";
		foreach($store->get(array('value','key')) as $k=>$v){
			$output.= "\$store->set('$v',\$$k); \n";
		}
		$output.= "?>";
		$output.= $Tpl->compile( $text );
		$output.= "<?php \n";
		$output.= 'unset($from);'."\n";
		$output.= 'unset($key);'."\n";
		$output.= 'unset($value);'."\n";
		$output.= "endforeach; \n";
		$output.= "?>";

		return $output;
	}
}
?>
