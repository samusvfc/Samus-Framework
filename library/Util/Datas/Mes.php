<?php
class Util_Datas_Mes {

	const JANEIRO = "janeiro";

	const FEVEREIRO = "fevereiro";

	const MARCO = "março";

	const ABRIL = "abril";

	const MAIO = "maio";

	const JUNHO = "junho";

	const JULHO = "julho";

	const AGOSTO = "agosto";

	const SETEMBRO = "setembro";

	const OUTUBRO = "outubro";

	const NOVEMBRO = "novembro";

	const DEZEMBRO = "dezembro";

	/**
	 * Retorna um array associativo com todos os meses do ano começando de 1
	 * $meses["janeiro"] = 1;
	 * @return int
	 */
	public static function getMesesAssociativeArray($keyIsNum = false) {
		$meses = array();
		if($keyIsNum) {
			$meses[1] = self::JANEIRO;
			$meses[2] = self::FEVEREIRO;
			$meses[3] = self::MARCO;
			$meses[4] = self::ABRIL;
			$meses[5] = self::MAIO;
			$meses[6] = self::JUNHO;
			$meses[7] = self::JULHO;
			$meses[8] = self::AGOSTO;
			$meses[9] = self::SETEMBRO;
			$meses[10] = self::OUTUBRO;
			$meses[11] = self::NOVEMBRO;
			$meses[12] = self::DEZEMBRO;
		} else {
			$meses[self::JANEIRO] = 1;
			$meses[self::FEVEREIRO] = 2;
			$meses[self::MARCO] = 3;
			$meses[self::ABRIL] = 4;
			$meses[self::MAIO] = 5;
			$meses[self::JUNHO] = 6;
			$meses[self::JULHO] = 7;
			$meses[self::AGOSTO] = 8;
			$meses[self::SETEMBRO] = 9;
			$meses[self::OUTUBRO] = 10;
			$meses[self::NOVEMBRO] = 11;
			$meses[self::DEZEMBRO] = 12;
		}
		return $meses;
	}

	/**
	 * Retorna um array dos messes do ano no formato array("janeiro" , "fevereiro"...
	 * @return string
	 */
	public static function getMesesArray() {
		return array(
					self::JANEIRO , 
					self::FEVEREIRO , 
					self::MARCO , 
					self::ABRIL , 
					self::MAIO , 
					self::JUNHO , 
					self::JULHO , 
					self::AGOSTO , 
					self::SETEMBRO , 
					self::OUTUBRO , 
					self::NOVEMBRO , 
					self::DEZEMBRO);
	}

	/**
	 * Retorna o mes corrente
	 * @return int
	 */
	public function getMesCorrenteNum() {
		return (int) date("m");
	}

	/**
	 * Retorna o nome do mês atual
	 * @return string
	 */
	public function getMesCorrenteNome() {
		$meses = $this->getMesesArray();
		$esseMes = $this->getMesCorrenteNum();
		$esseMes = $esseMes + 1;
		return $meses[$esseMes];
	}

	/**
	 * Retorna o nome do mes a partir de um numero de 1 - 12
	 * @param int $mesNum
	 * @return string
	 */
	public function getMesNome($mesNum) {
		$mesNum = $mesNum - 1;
		$meses = $this->getMesesArray();
		return $meses[$mesNum];
	}

	/**
	 * Reotorna o numero de dias do mes especificado, caso mes e ano não sejam
	 * especificados será retorna o mes e ano do mes corrente
	 * @param int|string $mes=""
	 * @param int|string $ano=""
	 * @return int
	 */
	public function getNumeroDeDiasDoMes($mes = "", $ano = "") {
		if(empty($mes))
			$mes = (int) $this->getMesCorrenteNum();
		if(empty($ano))
			$ano = date("Y");
		return (int) date("t", strtotime("$ano-$mes-01 00:00:00"));
	}
}
?>
