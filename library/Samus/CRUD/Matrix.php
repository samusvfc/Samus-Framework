<?php
/**
 * Objeto Array com diversos mщtodos especializados para  manipulaчуo do array 
 *  
 * @author Vinicius Fiorio - Samusdev@gmail.com
 * @category Arrays
 */
class Samus_CRUD_Matrix extends ArrayObject {

	/**
	 * Construtor da matrix
	 * @param $array array opcional
	 */
	public function __construct(array $array = array()) {
		parent::__construct($array);
	}

	/**
	 * Adiciona um ou vсrios valores ao final da matriz
	 * @param $value1 mixed valor para ser adicionado
	 * @param $value2...
	 * @return Samus_CRUD_Matrix
	 */
	public function add($value1) {
		foreach(func_get_args() as $arg) {
			$this->append($arg);
		}
		return $this;
	}
	
	/**
	 * Adiciona um ou varios elementos no inicio da Samus_CRUD_Matrix
	 * @param $value1 mixed
	 * @param $value2...
	 * @return Samus_CRUD_Matrix
	 */
	public function pop($value1) {
		$a = $this->getArrayCopy();
		$this->removeAll();
		
		//adiciona os novos valores
		foreach(func_get_args() as $arg) {
			$this->append($arg);
		}
		
		foreach ($a as $b) {
			$this->append($b);
		}
		return $this;
	}
	

	/**
	 * Adiciona uma ou vсrias arrays transformando seus elementos em itens
	 * @param $array1 mixed valor para ser adicionado
	 * @param $array2...
	 * @return Samus_CRUD_Matrix
	 */
	public function addArrayValues($array1) {
		foreach(func_get_args() as $arg) {
			foreach($arg as $a) {
				$this->append($a);
			}
		}
		return $this;
	}

	/**
	 * Remove todas as ocorrencias dos valores especificados
	 * @param $value1 mixed
	 * @param $value2...
	 * @return Samus_CRUD_Matrix
	 */
	public function removeValues($value1) {
		foreach(func_get_args() as $arg) {
			$pos = array_keys($this->getArrayCopy() , $arg);
			foreach($pos as $p) {
				unset($this[$p]);
			}
		}
		return $this;
	}

	/**
	 * Remove todas as chaves da Maitiz
	 * @param $key1 int|string
	 * @param $key2...
	 * @return Samus_CRUD_Matrix
	 */
	public function removeKeys($key1) {
		foreach(func_get_args() as $arg) {
			if(key_exists($arg , $this->getArrayCopy())) {
				unset($this[$arg]);
			}
		}
		return $this;
	}
	
	/**
	 * Limpa a Samus_CRUD_Matrix desalocando todos os seus dados
	 * @return Samus_CRUD_Matrix
	 */
	public function removeAll() {
		while($this->getIterator()->valid()) {
			unset($this[$this->getIterator()->key()]);
			$this->getIterator()->next();
		}
		return $this;
	}


	/**
	 * Alias para natsort()
	 * @return Samus_CRUD_Matrix
	 */
	public function orderByValue() {
		$this->natsort(); 
		return $this;
	}
	
	/**
	 * Invert a ordem da Samus_CRUD_Matrix
	 * @return Samus_CRUD_Matrix
	 */
	public function invert() {
		$a = array_reverse($this->getArrayCopy());
		$this->removeAll();
		foreach ($a as $b) {
			$this->add($b);
		}
		return $this;
	}
	

	/**
	 * Executa uma determinada funчуo em todos os elementos do array
	 * 
	 * Ex.:
	 * function teste($val) {
	 * 	echo $val;
	 * }
	 * 
	 * $matrix->each("teste");
	 * 
	 * @param $function 
	 * @return Samus_CRUD_Matrix
	 */
	public function each($function) {
		/**
		 * @var ArrayIterator
		 */
		$i = $this->getIterator();
		while($i->valid()) {
			call_user_func($function,$i->current(),$i->key());
			$i->next();
		}
		return $this;
	}
	
	/**
	 * Cast de tipo para Samus_CRUD_Matrix
	 * @param $matrix
	 * @return Samus_CRUD_Matrix
	 */
	public static function cast(Samus_CRUD_Matrix $matrix) {
		return $matrix;
	}

	/**
	 * Exibe todos os valores separados por virgula
	 */
	public function __toString() {
		$str = "";
		$i = $this->getIterator();
		while($i->valid()) {
			$str .= $i->current().', ';
			$i->next();
		}
		return $str;
	}
	
}

?>