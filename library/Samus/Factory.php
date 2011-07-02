<?php

/**
 * @author Vinicius Fiorio - samusdev@gmail.com
 */
class Samus_Factory extends Samus_Object {
    
	/**
	 * Obtem uma instancia de um objeto a partir do nome de uma classe, 
	 * Para facilitar a nomenclatura underscores podem ser substituidos por pontos
	 * 
	 * Samus_Factory::loadClass("Register.Person.Client");
	 * 
	 * @param unknown_type $className
	 */
	public static function loadClass($className) {
		$class = str_replace(".", "_", $className);
		return new $class;
	}

}