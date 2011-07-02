<?php //Criado em 19/01/2008 às 14:09:10 - Developer [ s a m u s ] - www.Samusdev.com.br


/* Classe de paginação de resultados
 * 	 Construtor: pede como parametro o nome da Tabela, se tiver, algum filtro de resultados (WHERE DO SQL) e o total e resultados por apgina
 * 	 o contrutor seta as propridades "inicio" e "limite" que devem ser usada para paginação
 * */
class Util_Paginacao {

	/**
	 * Nome da tabela no banco
	 *
	 * @var string
	 */
	private $tabela;

	/**
	 * Total de resultados 
	 *
	 * @var int
	 */
	private $totalResultados;

	/**
	 * Numero da página corrrente atual
	 *
	 * @var int
	 */
	private static $pagina = 0;

	/**
	 * Valor total de itens por página
	 *
	 * @var int
	 */
	private $total = 20;

	/**
	 * Numero com a posiçao inicial que deve ser exibida
	 *
	 * @var int
	 */
	public static $inicio;

	/**
	 * Numero com a posição limite dos itens que devem ser exibidos
	 *
	 * @var int
	 */
	public static $limite;

	/**
	 * Classe CSS que formata a páginação (aplicada ao <a>)
	 *
	 * @var string
	 */
	private $cssPaginacao = "paginacao"; //nome da classe do css que formata a pginação

	/**
	 * Classse CSS para itens marcados na páginação
	 *
	 * @var string
	 */
	private $cssPaginacaoMarcada = "cssPaginacaoMarcada";
	
	/**
	 * URL onde é processada a paginação
	 *
	 * @var string
	 */
	private $url;
	
	/**
	 * Numero com a posiçao inicial que deve ser exibida
	 * @global 
	 * @static 
	 * @final 
	 */
	const SESSION_INICIO_VAR = "pag_inicio";
	
	/**
	 *  Numero com a posição limite dos itens que devem ser exibidos
	 *  @global
	 *  @static 
	 * 	@final  
	 */
	const SESSION_LIMITE_VAR = "pag_limite";

	/**
	 * Construtor da clase paginação
	 *
	 * @param string $tabela nome da tabela no banco
	 * @param string $filtros filtros para o WHERE do select
	 * @param string $total total de itens por página
	 * @param string $pagina página autal da paginação (opcional usado para corrigir bugs)
	 * @return Util_Paginacao
	 */
	function Util_Paginacao($tabela, $filtros, $total,$pagina="") {
		$this->tabela = $tabela;
		
		if(! empty($total))
			$this->total = $total;
		
		$this->totalResultados = $this->setTotalResultados($filtros);
		
		if(empty($pagina))
			$pagina = $_GET["pag"];
		
		if(! empty($pagina))
			$this->pagina = $pagina;
		
		self::$inicio = $this->pagina * $this->total;
		self::$limite = $this->total;
	
		$_SESSION[self::SESSION_INICIO_VAR] = self::$inicio;
		$_SESSION[self::SESSION_LIMITE_VAR] = self::$limite;	

	}

	public function setTotalResultados($filtros) {
		$q1 = "SELECT COUNT(*) ";
		$q1 .= "FROM $this->tabela ";
		
		if(! empty($filtros))
			$q1 .= " WHERE $filtros ";
		
		$r1 = mysql_query($q1) or die(mysql_error());
		$v1 = mysql_fetch_array($r1, MYSQL_NUM);
		return $v1[0];
	
	}

	/**
	 * Exibe a paginação
	 *
	 * @param string $getValue
	 */
	public function exibirPaginacao($getValue="") {
		$totalPaginas = $this->totalResultados / $this->total;
		$totalPaginas = ceil ($totalPaginas);
		
		$str = "";
		
		if($totalPaginas > 1) {
			
			if($this->pagina != 0)
				$anterior = $this->pagina - 1;
			else
				$anterior = $totalPaginas - 1;
			
			$str .= "<a  class='$this->cssPaginacao' href='" 
				. $this->getUrl() . "-$anterior";
			if(! empty($getValue))
				$str .= "-" . $getValue;
			$str .= "'>";
			$str .= "Anterior</a>";
			
			for($i = 0; $i < $totalPaginas; $i ++) {
				
				if($i == $this->pagina)
					$marca = $this->cssPaginacaoMarcada;
				else
					$marca = "";
				
				$str .= "<a  class='$this->cssPaginacao $marca' href='" 
				. $this->getUrl() . "-$i";
				
				if(! empty($getValue))
					$str .= "-" . $getValue;
				
				$str .= "'>";
				$str .= $i + 1;
				$str .= "</a>";
			}
			
			if($this->pagina + 1 != $totalPaginas)
				$prSamus_Controllerima = $this->pagina + 1;
			else
				$prSamus_Controllerima = 0;
			
			$str .= "<a  class='$this->cssPaginacao' href='" 
			. $this->getUrl() . "-$prSamus_Controllerima";
			if(! empty($getValue))
				$str .= "-" . $getValue;
			$str .= "'>";
			$str .= "Próxima</a>";
			
			$str .= "<div style='clear: both;'></div>";
		}
		
		return $str;
	}

	
	/**
	 * @return string
	 */
	public function getCssPaginacao() {
		return $this->cssPaginacao;
	}

	/**
	 * @return string
	 */
	public function getCssPaginacaoMarcada() {
		return $this->cssPaginacaoMarcada;
	}

	/**
	 * @return int
	 */
	public static function getInicio() {
		return self::$inicio;
	}

	/**
	 * @return int
	 */
	public static function getLimite() {
		return self::$limite;
	}

	/**
	 * @return int
	 */
	public static function getPagina() {
		return $this->pagina;
	}

	/**
	 * @return string
	 */
	public function getTabela() {
		return $this->tabela;
	}

	/**
	 * @return int
	 */
	public function getTotal() {
		return $this->total;
	}

	/**
	 * @return int
	 */
	public function getTotalResultados() {
		return $this->totalResultados;
	}

	/**
	 * @param string $cssPaginacao
	 */
	public function setCssPaginacao($cssPaginacao) {
		$this->cssPaginacao = $cssPaginacao;
	}

	/**
	 * @param string $cssPaginacaoMarcada
	 */
	public function setCssPaginacaoMarcada($cssPaginacaoMarcada) {
		$this->cssPaginacaoMarcada = $cssPaginacaoMarcada;
	}

	/**
	 * @param int $inicio
	 */
	public static function setInicio($inicio) {
		self::$inicio = $inicio;
	}

	/**
	 * @param int $limite
	 */
	public static function setLimite($limite) {
		self::$limite = $limite;
	}

	/**
	 * @param int $pagina
	 */
	public static function setPagina($pagina) {
		self::$pagina = $pagina;
	}

	/**
	 * @param string $tabela
	 */
	public function setTabela($tabela) {
		$this->tabela = $tabela;
	}

	/**
	 * @param int $total
	 */
	public function setTotal($total) {
		$this->total = $total;
	}

	
	/**
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

}


?>
