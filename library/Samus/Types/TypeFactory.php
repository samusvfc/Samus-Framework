<?php

/**
 * @author Vinicius Fiorio - samusdev@gmail.com
 */
class TypeFactory {
    
    public static function getInstance($className , array $args = array()) {
	$ref = new ReflectionClass($className);
	$obj = null;
	$strEval = '$obj = new ' . ucfirst($className) . "(";

	foreach($args as $key => $arg) {
	    $strEval .= $arg . ',';
	}

	$strEval = substr($strEval, 0 , -1);


	$strEval .= ");";
	eval ($strEval);

	echo $strEval;

	return $obj;
    }

}
?>
