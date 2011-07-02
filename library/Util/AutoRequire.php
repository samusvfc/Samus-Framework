<?php

/**
 * Inclui automaticamente arquivos que utilizam o padrão de underscores para 
 * especificar seus diretorios
 * 
 * Util_Person_Register = Util/Person/Register.php
 * 
 * @author Vinicius Fiorio - samus@samus.com.br
 */
class Util_AutoRequire {
	
	private static $showSuggests = false;
	private static $pathArray;
	
	public static function requireFile($className, $defaultClassExtension = '.php') {
		
		$fileArray = explode("_", $className);
		$requireString = "";
		
		//faço o include do smarty
		if (strtolower($fileArray [0]) == 'smarty') {
			$_class = strtolower($className);
			if (substr($_class, 0, 16) === 'smarty_internal_' || $_class == 'smarty_security') {
				$filename = SMARTY_SYSPLUGINS_DIR . $_class . '.php';
				if (self::isFile($filename)) {
					return $filename;
				} else {
					return false;
				}
			}
		} else {
			
			//faço o include de outros tipos de arquivos
			/*
			foreach ($fileArray as $key => $f ) {
				$requireString .= $f . '/';
			}*/
			
			$requireString = implode('/', $fileArray) . $defaultClassExtension;
			
			if (! self::isFile($requireString)) { // caso não seja um arquivo, verifico se ele é o arquivo principal da pasta
				$requireString = implode('/', $fileArray) . '/' . $fileArray [count($fileArray) - 1] . $defaultClassExtension;
				if (self::isFile($requireString))
					return $requireString;
				else
					return false;
			} else {
				return $requireString;
			}
		
		}
	
	}
	
	/**
	 * Inclui uma classe qualquer buscando pela extensão de arquivo default de
	 * classes no PHP
	 *
	 * @param string $className
	 * @param string $defaultClassExtension .php
	 * @return boolean
	 */
	public static function requireClass($className, $defaultClassExtension = ".php") {
		//se a classe ja existir nem inclui
		if (! class_exists($className)) {
			
			$filePath = self::requireFile($className, $defaultClassExtension);
			if ($filePath) {
				require_once $filePath;
			}
			
			if (self::$showSuggests) {
				echo "require_once '" . $filePath . "';<br />
";
			}
		} else {
			return true;
		}
	}
	
	/**
	 * Exibe como string na tela os arquivos incluidos automaticamente
	 */
	public static function isSuggestsVisible() {
		self::$showSuggests = false;
	}
	
	/**
	 * Esconde os requires automaticos
	 */
	public static function hideSuggest() {
		self::$showSuggests = false;
	}
	
	/**
	 * Verifica se o arquivo existe considerando o include path
	 */
	public static function isFile($filename) {
		// Check for absolute path
		if (empty(self::$pathArray)) {
			self::$pathArray = explode(PATH_SEPARATOR, get_include_path());
		}
		
		if (is_file($filename)) {
			return true;
		}
		
		foreach ( self::$pathArray as $p ) {
			$completeFileName = $p . $filename;
			if (is_file($completeFileName)) {
				return true;
			}
		}
		
		return false;
	}

}