<?php
require_once 'foto/ImgObject.php';

class Img_Dir {

	private $dir = array();

	private $formatosPermitidos = array("jpg" , "jpeg" , "png" , "gif");
	
	private $imgCount = 0;
	
	public function __construct($dir) {
		$this->setDir($dir);
	}

	/**
	 * Obtem um array de ImgObject com todos os arquivos de imagens dos 
	 * diretórios especificados
	 * 
	 * @param int|null $limit
	 * @return array
	 */
	public function getImgArray($limit=null) {
		$imgArray = array();
		
	
		foreach($this->getDir() as $diretorio) {
			
			$imgArray = array();
			
			$d = new DirectoryIterator($diretorio);
	
			while($d->valid()) { 
	
				if(!$d->isDot()) {
					$fileName = $d->getFilename();
					if($this->validateFormat($fileName)) {
						$imgObj = new ImgObject($fileName , $diretorio);
						
						$imgArray[] = $imgObj;
						++$this->imgCount;
						
						if($limit != null) {
							if($this->imgCount >= $limit) {
								break;
							}
						}
						
					}
				}
				$d->next();
			}
			
		}
		
		return $imgArray;
	}
	
	/**
	 * Execute getImgArray() antes para obter a contagem de arquivos de imagens processadas
	 * @return int
	 */
	public function getTotalImgs() {
		return $this->imgCount;
	}
	


	/**
	 * Valida um formato de arkivo a partir do ultimo sufixo do nome 
	 * @param $filename
	 */
	protected function validateFormat($filename) {
		$name_array = explode("." , $filename);
		$formato = strtolower( array_pop($name_array) );
		
		$valido = false;
		foreach($this->getFormatosPermitidos() as $formatoValido) {
			if($formato == strtolower($formatoValido)) {
				$valido = true;
				break;
			}
		}
		
		return $valido;
	}

	/**
	 * Obtem um array dos formatos de arquivos permitidos
	 * @return array
	 */
	public function getFormatosPermitidos() {
		return $this->formatosPermitidos;
	}

	/**
	 * Especifica os formatos de arquivo permitido
	 * @param array $formatosPermitidos
	 */
	public function setFormatosPermitidos(array $formatosPermitidos) {
		$this->formatosPermitidos = $formatosPermitidos;
	}

	/**
	 * Adiciona um ou mais formato de arquivo permitido
	 * @param $formato1
	 * @param $formato2 ...
	 */
	public function addFormatoPermitido($formato1) {
		foreach(func_get_args() as $arg) {
			$this->formatosPermitidos[] = $arg;
		}
	}

	/**
	 * Obtem o diretorio analizado
	 * @return array array com os diretorios analizados
	 */
	public function getDir() {
		return $this->dir;
	}

	/**
	 * Especifica um diretório que será analizado
	 * @param $dir string
	 */
	public function setDir($dir) {
		$this->dir = array($dir);
	}
	
	/**
	 * Especifca um array de diretorio para serem analizados
	 * @param $dir_array
	 */
	public function setDirArray(array $dir_array) {
		$this->dir = $dir_array;
	}
	
	
	/**
	 * Adiciona um diretorio para ser analizado
	 * @param $dir1
	 */
	public function addDir($dir1) {
		foreach (func_get_args() as $arg) {
			$this->dir[] = $arg;
		}
	}
}

?>