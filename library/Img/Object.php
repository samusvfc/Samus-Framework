<?php


class Img_Object extends Properties {
	
	/**
	 * Nome do arquivo
	 * @var string
	 */
	private $fileName;
	
	/**
	 * Diretorio onde o arquivo se encontra
	 * @var  string
	 */
	private $directory;
	
	/**
	 * Caminho completo para o arquivo diretorio + arquivo
	 * @var string
	 */
	private $file;
	
	/**
	 * Tipo do arquivo baseado no sufixo do nome do arquivo
	 * @var string
	 */
	private $type;
	
	/**
	 * Mime type do arquivo 
	 * @var string
	 */
	private $mime;
	
	/**
	 * Tamanho em bytes 
	 * @var int
	 */
	private $size;
	
	/**
	 * Largura calculada automaticamente
	 * @var string
	 */
	private $width;
	
	/**
	 * Altura da imagem calculada automaticamente com getimagesize
	 * @var string
	 */
	private $height;
	

	/**
	 * Costrutor responsсvel pela especificaчуo dos dados
	 * @param string $filename
	 * @param string $directory
	 */
	public function __construct($filename , $directory) {
		$directory .= "/";
		$file =   $directory . $filename;
		
		$infos = getimagesize($file);
		
		$this->width = $infos[0];
		$this->height = $infos[1];
		$this->mime = $infos["mime"];
		
		$this->size = filesize($file);
		
		$this->fileName = $filename;
		$this->directory = $directory;
		$this->file = $file;
	}
	
	/**
	 * Casting de tipo
	 * @param Img_Object $imgObject
	 * @return Img_Object
	 */
	public static function cast(Img_Object $imgObject) {
		return $imgObject;
	}
	
	/**
	 * Obtem o caminho COMPLETO da imagem
	 * @return string
	 */
	public function getFile() {
		return $this->file;
	}
	
	/**
	 * Obtem o nome do arquivo de imagem
	 * @return string
	 */
	public function getFileName() {
		return $this->fileName;
	}
	
	/**
	 * Obtem o diretorio da imagem
	 * @return string
	 */
	public function getDirectory() {
		return $this->directory;
	}
	
	
	/**
	 * Obtem o tipo de arquivo baseado no sufixo do nome
	 * @return string
	 */
	public function getType() {
		
		if(empty($this->type)) {
			$name_array = explode("." , $this->filename);
			$this->type = strtolower( array_pop($name_array) );
		}
		return $this->type;
		
	}
	
	/**
	 * Obtem o mime do arquivo
	 * @return string
	 */
	public function getMime() {
		$file = $this->directory . $this->fileName;
		return mime_content_type($file);
	}
	
	/**
	 * Obtem a largura da imagem
	 * @return string width
	 */
	public function getWidth() {
		return $this->width;
	}
	
	/**
	 * Obtem a altura da imagem
	 * @return string height
	 */
	public function getHeight() {
		return $this->height;
	}
	
	/**
	 * Obtem a largura e altura no formato: " width=100 height=100 "
	 * @return string
	 */
	public function getWidthHeight() {
		return " width=".$this->getWidth()." height=".$this->getHeight();
	}
	
	/**
	 * Obtem o tamanho do arquivo em bytes
	 * @return int
	 */
	public function getSize() {
		return $this->size;
	}
	
	
	
}

?>