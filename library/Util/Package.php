<?php

/**
 * Carrega classes e arquivos de um determinado diretório considerando o classpath atual do arquivo
 * @author Vinicius Fiorio - Samusdev@gmail.coms
 *
 */
class Util_Package {

	public static $filesExtensions = array (".php", ".php" );
	public static $classPath = array ();

	/**
	 * Obtem o classpath atual e transforma em um array
	 *
	 */
	public static function getCurrentPath() {
		self::$classPath = explode ( PATH_SEPARATOR, get_include_path () );
	}

	/**
	 * Carrega todos os arquivos do pacote especificado o carregamento considera o classpath atual da execusão
	 *
	 * @param string $package nome do pacote
	 */
	public static function load($package) {
		self::getCurrentPath ();

		if (substr ( $package, - 1, 1 ) != "/") {
			$package .= "/";
		}

		foreach ( self::$classPath as $path ) {

			$dir = $path . $package;

			if (is_dir ( $dir )) {

				$d = new DirectoryIterator ( $dir );

				while ( $d->valid () ) {

					if ($d->isFile ()) {

						$requireThis = false;
						//testa a extensão
						foreach ( self::$filesExtensions as $ext ) {
							if (substr ( $d->getFilename (), strlen ( $ext ) * - 1, strlen ( $ext ) ) == $ext) {
								$requireThis = true;
								break;
							}
						}

						if ($requireThis) {
							require_once ($package . $d->getFilename ());
						}
					}

					$d->next ();
				}

			}
		}

	}

}

?>