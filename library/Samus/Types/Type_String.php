<?php
require_once 'Samus/types/DataTypeInterface.php';

/**
 * Implementação do tipo primitibvo string, adicionando todos os métodos mais 
 * comuns
 *
 * @author Vinicius Fiorio - samusdev@gmail.com
 */
class Type_String implements DataTypeInterface {

    /**
     * String que será manipulada com a classe
     * @var string
     */
    public $string;

    /**
     * @param string $string
     */
    public function  __construct($string="") {
        $this->setString($string);
    }

    /**
     * Especifica a string
     * @param string $string
     */
    public function setString($string) {
        $this->string = (string) $string;
    }

    /**
     * Concatena uma ou mais strings
     *
     * @param string|int|float|object $string1
     * @param string|int|float|object $string2 ...
     * @return Str
     */
    public function concat($string1) {
        foreach(func_get_args() as $arg) {
            $this->string .= $arg;
        }

        return $this;
    }

    /**
     * Corta uma string
     *
     * @param int $start
     * @param int $length
     * @return Str
     */
    public function substr($start , $length="") {
        $this->string = substr($this->string, $start , $length);
        return $this;
    }

    /**
     * Busca uma string($search) e substitui por outra string($replace)
     *
     * @param string $search
     * @param string $replace
     * @param boolean $caseSensitive = true
     * @return Str
     */
    public function replace($search, $replace,$caseSensitive = true) {
        if($caseSensitive) {
            $this->string = str_replace($search, $replace, $this->string);
        } else {
            $this->string = str_ireplace($search, $replace, $this->string);
        }
        return $this;
    }

	/**
	 * Remove as tas especiais
	 *
	 * @param string|null $allowable_tags
	 * @return string
	 */
    public function stripTags($allowable_tags=null) {
        $this->string = strip_tags($this->string, $allowable_tags);
        return $this;
    }

	/**
	 * Transforma a string em um array
	 *
	 * @param int $split_length
	 * @return Samus_CRUD_Matrix
	 */
    public function toArray($splitLength = null) {
        return new Samus_CRUD_Matrix(str_split($this->string, $splitLength));
    }
	
    /**
     *
     * @return Str
     */
    public function shuffle() {
        return new Str(str_shuffle($this->string));
    }

    public function length() {
        return new Str(strlen($this->string));
    }

	/**
	 * Obtem todos os numeros float que estiverem na string
	 * @return Number
	 */
    public function getFloatNumbers() {
        return new Number(filter_var($this->string,FILTER_SANITIZE_NUMBER_FLOAT));
    }

	/**
	 * Obtem os numeros inteiros de uma strings
	 * @return Number
	 */
    public function getItegerNumbers() {
        return new Number(filter_var($this->string , FILTER_SANITIZE_NUMBER_INT));
    }

	/**
	 * Limpa completamente uma string removendo todas as tags e caracteres especiais
	 *
	 * @return Str
	 */
    public function clean() {
        $this->string = filter_var($this->string, FILTER_SANITIZE_STRING);
        return $this;
    }

	/**
	 * Valida se a string é um email
	 * @return boolean
	 */
    public function isEmail() {
        $result = filter_var($this->string, FILTER_VALIDATE_EMAIL);
        if(is_string($result)) {
            return true;
        } else {
            return false;
        }
    }

	/**
	 * Valuda se a string é uma URL
	 * @return boolean
	 */
    public function isURL() {
        $result = filter_var($this->string, FILTER_VALIDATE_URL);
        if(is_string($result)) {
            return true;
        } else {
            return false;
        }
    }

	/**
	 * Valida se a string é um IP
	 * @return boolean
	 */
    public function isIp() {
        $result = filter_var($this->string, FILTER_VALIDATE_IP);
        if(is_string($result)) {
            return true;
        } else {
            return false;
        }

    }

	/**
	 * Adiciona barras invertidas nas aspas
	 * @return Str
	 */
    public function scapeSlashes() {
        $this->string = addslashes($this->string);
        return $this;
    }

	/**
	 * Converte caracteres especiais para a realidade HTML
	 * - Aplica htmlspecialchars
	 * @return Str
	 */
    public function htmlSpecialChars($charSet="ISO-8859") {
        $this->string = htmlspecialchars($this->string,ENT_QUOTES,$charSet);
        return $this;
    }

	/**
	 * Converte a Str para UTF-8
	 * @return Str
	 */
    public function toUtf8() {
        $this->string = utf8_encode($this->string);
        return $this;
    }

	/**
	 * Insere quebras de linha HTML antes de todas newlines em uma string
	 * @return string
	 */
    public function newLineToBr() {
        $this->string = nl2br($this->string);
        return $this;
    }

	/**
	 * Exibe a string (print)
	 */
    public function printString() {
        print($this->string);
    }

	/**
	 * Exibe a string (echo)
	 */
    public function echoString() {
        echo $this->string;
    }

	/**
	 * Converte a string para minusculo
	 * @return Str
	 */
    public function lowerCase() {
        $this->string = strtolower($this->string);
        return $this;
    }

	/**
	 * Converte a string para maiusculo
	 * @return Str
	 */
    public function upperCase() {
        $this->string = strtoupper($this->string);
        return $this;
    }

	/**
	 * Converte para maiusculo todas as primeiras letras da string
	 * @return Str
	 */
    public function upperFirstLettersToUpper() {
        $this->string = ucwords($this->lowerCase());
        return $this;
    }

	/**
	 * Converte os caracteres maiusculos em minusculos adicionando um underline
	 * como espaço entre as palavras (o underline pode ser substituido caso o
	 * divisor seja alterado)
	 *
	 * @param string $divisor divisor entre as palavras
	 * @return Str
	 */
    public function upperToUnderline($divisor="_") {
        $string = $this->string;
	    
        $string = strtolower($string{0}).substr($string,1);
        foreach(str_split($string) as $caracter) {
            if($caracter != $divisor)
            $string = str_replace(strtoupper($caracter) , $divisor.strtolower($caracter) , $string);
        }
		
        $this->string = $string;
        return $this;
    }

	/**
	 * Substitui a notação camelCase para separação com espaços
	 * @return Str
	 */
    public function upperToSpace() {
        $this->string = $this->upperToUnderline(" ");
        return $this;
    }

   /**
	* Converte a nomenclatura de underlines para nomenclatura de letra maiuscula
	* para cada palavra da expressão
	*
	* @param string $divisor
	* @return Str
	*/
    public function underlineToUpper($divisor="_") {
		
        $string = $this->string;

        $array = str_split($string);
        $ai = new ArrayIterator($array);

        $string = "";
        while ($ai->valid()) {

            if($ai->current() == $divisor) {
                $ai->next();
                $string .= strtoupper($ai->current());
            } else {
                $string .= $ai->current();
            }

            $ai->next();
        }

        $this->string = $string;
        return $this;
    }

  /**
	* Converte a nomenclatura de underlines para nomenclatura legivel colocando
	* espaços no lugar das underline
	*
	* @param string $divisor
	* @return Str
	*/
    public function underlineToSpace($divisor="_") {

        $string = $this->string;

        $string = ucfirst($string);
        $array = str_split($string);
        $ai = new ArrayIterator($array);

        $string = "";
        while ($ai->valid()) {

            if($ai->current() == $divisor) {
                $ai->next();
                $string .= " ".strtoupper($ai->current());
            } else {
                $string .= $ai->current();
            }

            $ai->next();
        }

        $this->string = $string;
        return $this;
    }


	/**
	 * Adiciona 0 (zeros) no inicio da string para formatação até a largura máxima
	 * estipulada
	 *
	 * @param int $limitSize
	 * @return Str
	 */
    public function addZerosToString($limitSize) {
        while ($this->length() < $limitSize) {
            $this->string = "0".$this->string;
        }
        return $this;
    }


	 /**
	 * Obtem a primeira ocorrencia de uma string
	 * 
	 * @param string $haystack string de origem
	 * @param string $$busca string para buscar na origem
	 * @param boolean $showBeforeString se irá retornar tudo que esta antes da string
	 * @return string
	 */
    public function strstr($busca, $showBeforeString=FALSE) {
        $haystack = $this->string;
		
        if(($pos=strpos($haystack,$$busca))===FALSE) return FALSE;

        if($showBeforeString) {
            $this->string = substr($haystack,0,$pos+strlen($$busca));
            return $this;
        } else {
            $this->string = substr($haystack,$pos);
            return $this;
        }
    }
	
	
	/**
	 * Lima a string no inicio e no final, retirando quebras de linha e espaços
	 * em branco
	 * 
	 * @return Str
	 */
    public function trim() {
        $this->string = trim($this->string);
        return $this;
    }
	
	/**
	 * Quebra uma string em linhas usando um caractere de quebra de linha. 
	 * @param string $width
	 * @param string $lineBreackType
	 * @param boolean $cutWords
	 * @return Str
	 */
    public function warp($width=80 , $lineBreakType = "<br />" , $cutWords=false) {
        $this->string = wordwrap($this->string , $width , $lineBreakType , $cutWords);
        return $this;
    }
	
	/**
	 * Testa se a string esta vazia
	 * @return boolean
	 */
    public function isEmpty() {
        return empty($this->string);
    }
	
	/**
	 * Converte uma string para ISO8859-1
	 * @return Str
	 */
    public function toISO8859_1() {
        $this->string = unicode_encode ($this->string , 'ISO-8859-1');
        return $this;
    }
	
	/**
	 * Codifica a string para uma codificação qualquer
	 * @param $encoding
	 * @return Str
	 */
    public function encode($encoding = 'ISO-8859-1') {
        $this->string = unicode_encode ($this->string , $encoding);
        return $this;
    }
	
	
	/**
	 * Converte um array para um astring, juntando cada elemento com "divisor"
	 * @param array $array
	 * @param string $divisor
	 * @return Str
	 */
    public function arrayToString($array , $divisor=" ") {
        $this->string = implode($divisro , $array);
        return $this;
    }
	
	/**
	 * Converte a String para uma notação de dinheiro
	 * @return Str
	 */
    public function moneyFormat() {
        $this->string = number_format($this->string , ",");
        return $this;
    }
	
	/**
	 * Obtem o valor Inteiro da String
	 * @return int
	 */
    public function getInt() {
        return (int) $this->string;
    }
	
	/**
	 * Obtem o valor float da string
	 * @return float
	 */
    public function getFloat() {
        return (float) $this->string;
    }
	
	/**
	 * Converte o hash md5 da string para 
	 * @return Str
	 */
    public function toMd5() {
        $this->string = md5($this->string);
        return $this;
    }

    /**
     * Aplica addslashes
     * @return Str
     */
    public function addSlashes() {
        $this->string = addslashes($this->string);
        return $this;
    }

    /**
     * Aplica FILTER_SANITIZE_STRING e stripTags e addSlashes
     * @return Str
     */
    public function sanitize() {
        $this->string = filter_var($this->string, FILTER_SANITIZE_STRING);
        $this->stripTags();
        $this->addSlashes();
        return $this;
    }
	
	/**
	 * Obtem a string manipulada
	 * @return string
	 */
    public function getString() {
        return $this->string;
    }
	
	/**
	 * Retorna a string manipulada
	 * @return string
	 */
    public function  __toString() {
        return (string) $this->string;
    }
	
    public function getType() {
        return "Str";
    }

    /**
     * Faz o casting de tipo do objeto
     * @param Str|string $object
     * @return Str
     */
    public static function cast($object) {
        if($object instanceof Str) {
            return $object;
        } else {
            return new Str((string) $object);
        }
    }

    /**
     * Obtem uma instancia de Str
     * @param string $string
     * @return Str
     */
    public static function getInstance($string="") {
        return new Str($string);
    }

}


?>
