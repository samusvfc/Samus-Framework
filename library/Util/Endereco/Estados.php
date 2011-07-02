<?php
class Util_Endereco_Estados {

	private $estados = array();

	const PAIS = "Brasil";

	public function __construct() {
		$this->estados = array();
		$this->estados[] = array('AC' , 'Acre');
		$this->estados[] = array('AL' , 'Alagoas');
		$this->estados[] = array('AP' , 'Amap�');
		$this->estados[] = array('AM' , 'Amazonas');
		$this->estados[] = array('BA' , 'Bahia');
		$this->estados[] = array('CE' , 'Cear�');
		$this->estados[] = array('DF' , 'Distrito Federal');
		$this->estados[] = array('ES' , 'Esp�rito Santo');
		$this->estados[] = array('GO' , 'Goi�s');
		$this->estados[] = array('MA' , 'Maranh�o');
		$this->estados[] = array('MT' , 'Mato Grosso');
		$this->estados[] = array('MS' , 'Mato Grosso do Sul');
		$this->estados[] = array('MG' , 'Minas Gerais');
		$this->estados[] = array('PE' , 'Pernambuco');
		$this->estados[] = array('PA' , 'Par�');
		$this->estados[] = array('PA' , 'Para�ba');
		$this->estados[] = array('PR' , 'Paran�');
		$this->estados[] = array('PI' , 'Piau�');
		$this->estados[] = array('RN' , 'Rio Grande do Norte');
		$this->estados[] = array('RS' , 'Rio Grande do Sul');
		$this->estados[] = array('RJ' , 'Rio de Janeiro');
		$this->estados[] = array('RO' , 'Rond�nia');
		$this->estados[] = array('RR' , 'Roraima');
		$this->estados[] = array('SP' , 'S�o Paulo');
		$this->estados[] = array('SC' , 'Santa Catarina');
		$this->estados[] = array('SE' , 'Sergipe');
		$this->estados[] = array('TO' , 'Tocantins');
		//natsort($this->estados);            
	}

	/**
	 * Retorna um array associatibo com os estados $estados["ES"] = "Esp�rito Santo";
	 * @return string
	 */
	public function getEstadosArray() {
		$ai = new ArrayIterator($this->getEstados());
		$estados = array();
		while ($ai->valid()) {
			$est = $ai->current();
			$estados[$est[0]] = $est[1];
			$ai->next();
		}
		return $estados;
	}

	public function getEstado($estadoNum) {
		return $this->estados[$estadoNum];
	}

	public function getEstados() {
		return $this->estados;
	}
}
?>
