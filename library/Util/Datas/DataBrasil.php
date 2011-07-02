<?php //Criado em 04/01/2008 às 09:15:36 - Developer [ s a m u s ] - www.Samusdev.com.br
class Util_Datas_DataBrasil {

	public static $estilos = array(1,2,3,4,5,6);

	public function getSemana($dia_da_semana) {
		switch($dia_da_semana) {
			case 0 : $d_semana = "Domingo";
			break;
			case 1 : $d_semana = "Segunda Feira";
			break;
			case 2 : $d_semana = "Terça Feira";
			break;
			case 3 : $d_semana = "Quarta Feira";
			break;
			case 4 : $d_semana = "Quinta Feira";
			break;
			case 5 : $d_semana = "Sexta Feira";
			break;
			case 6 : $d_semana = "Sábado";
			break;
		}
		return $d_semana;
	}

	public function getMes($mes_num) {
		switch($mes_num) {
			case 1 :
				$mes = "Janeiro";
				break;
			case 2 :
				$mes = "Fevereiro";
				break;
			case 3 :
				$mes = "Março";
				break;
			case 4 :
				$mes = "Abril";
				break;
			case 5 :
				$mes = "Maio";
				break;
			case 6 :
				$mes = "Junho";
				break;
			case 7 :
				$mes = "Julho";
				break;
			case 8 :
				$mes = "Agosto";
				break;
			case 9 :
				$mes = "Setembro";
				break;
			case 10 :
				$mes = "Outubro";
				break;
			case 11 :
				$mes = "Novembro";
				break;
			case 12 :
				$mes = "Dezembro";
				break;
		}

		return $mes;
	}

	public function getData($estilo) {
		 
		if(empty($estilo) or $estilo>6 or $estilo<1) {
			$estilo = 5;
		}

		if($estilo==1) {
			$var_data = date("d")."/".date("m")."/".date("y");
		}
		elseif($estilo==2) {
			$var_data = date("d")."-".date("m")."-".date("y");
		}
		elseif($estilo==3) {
			$var_data = date("d")."/".date("m")."/".date("Y");
		}
		elseif($estilo==4) {
			$var_data = date("d")."-".date("m")."-".date("Y");
		}
		elseif($estilo==5) {
			$var_data = $this->getSemana(date("w")).", ".date("d")."/".date("m")."/".date("Y");
		}
		elseif($estilo==6) {
			$var_data = $this->getSemana(date("w")).", ".date("d")." de ".$this->getMes(date("n"))." de ".date("Y");
		}
		return $var_data;
	}
}


?>
