<?php
require_once 'util/datas/Util_Datas_Mes.php';
/**
 * Util_Datas_Calendario - 27/05/2009
 * @author Vinicius Fiorio Custódio - samusdev@gmail.com
 */
class Util_Datas_Calendario {


    public $dia;
    public $mes;
    public $ano;
    public $tstamp;
    public $dtmanip;
    public $dsprimdia;
    public $linhafechada;

    const TABLE_CSS_CLASS = "calendar-table";

    const SELECTED_DAY_CSS_CLASS = "calendar-selected-day";

    const HOJE_CSS_CLASS = "calendar-today";

    const SEMANA_CSS_CLASS = "calendar-weeks";

    const MES_CSS_CLASS = "calendar-month";

    private $semana = array(
    "domingo" => "d" ,
    "segunda" => "s" ,
    "terca" => "t" ,
    "quarta" => "q" ,
    "quinta" => "q" ,
    "sexta" => "s" ,
    "sabado" => "s"
    );

    public function  __construct($pmes, $pano ) {
        $this->linhafechada = true;
        $this->dia = 1;
        $this->mes = $pmes;
        $this->ano = $pano;
        $this->calcula_tstamp();
        $this->data_manipulavel();
        $this->primeiro_dia_mes();
    }

    public function getSemanaFormat() {
        return $this->semana;
    }

    public function setSemanaFormat(array $semana) {
        $this->semana = $semana;
    }

    public function calcula_tstamp() {
        $this->tstamp = mktime( 0, 0, 0, $this->mes, $this->dia, $this->ano );
    }

    public function data_manipulavel() {
        $this->dtmanip = getdate( $this->tstamp );
    }

    public function primeiro_dia_mes() {
        $this->dsprimdia = $this->dtmanip[ "wday" ];
    }

    public function proximo_dia() {
        $this->dia++;
        $this->calcula_tstamp();
    }

    public function getCalendarioStr($url , $selectedDay) {
        $larg = 100.0/7.0;
        $str = "";


        $str .= "<table border='0' width='100%' cellpadding='0' cellspacing='0' align='center' class='".self::TABLE_CSS_CLASS."' >\n";
        $str .= "<thead>";
        $str .= "<tr>";
        $str .= "<th colspan=7>";

        $str .= "<div class='".self::MES_CSS_CLASS."'>";
        $str .= "<a title='Mês Anterior' href='$url-$this->mes-anterior-$this->ano' style='float: left; width: 20px; text-align: center;'> &laquo; </a>";
        $str .= "<a title='Próximo Mês' href='$url-$this->mes-proximo-$this->ano' style='float: right; width: 20px; text-align: center;'>&raquo;</a>";
        $str .= ucfirst(Util_Datas_Mes::getMesNome($this->mes)) . " " . $this->ano;
        $str .= "</div>";



        $str .= "</th>";
        $str .= "</tr>";
        $str .= "</thead>";
        $str .= "<tr class='".self::SEMANA_CSS_CLASS."'>\n";
        $str .= "<th align='center' width='".$larg."%'>".$this->semana['domingo']."</th>\n";
        $str .= "<th align='center' width='".$larg."%'>".$this->semana['segunda']."</th>\n";
        $str .= "<th align='center' width='".$larg."%'>".$this->semana['terca']."</th>\n";
        $str .= "<th align='center' width='".$larg."%'>".$this->semana['quarta']."</th>\n";
        $str .= "<th align='center' width='".$larg."%'>".$this->semana['quinta']."</th>\n";
        $str .= "<th align='center' width='".$larg."%'>".$this->semana['sexta']."</th>\n";
        $str .= "<th align='center' width='".$larg."%'>".$this->semana['sabado']."</th>\n";
        $str .= "</tr>\n";

        $ccol = 0;
        $casa = 0;
        while( checkdate( $this->mes, $this->dia, $this->ano ) ) {
            if ( $this->linhafechada ) {
                $str .= "<tr>\n";
                $this->linhafechada = false;
            }
            if ( $casa < $this->dsprimdia ) {
                $str .= "<td>&nbsp;</td>\n";
            } else {
                $str .= "<td align='center'>\n";


                if($this->dia == $selectedDay) {
                    $class = self::SELECTED_DAY_CSS_CLASS;
                } else {
                    $class = "";
                }

                $str .= "<a class='$class' title='Eventos Após $this->dia de ".ucfirst(Util_Datas_Mes::getMesNome($this->mes)) . " de " . $this->ano." ' href='$url-$this->mes-$this->dia-$this->ano'>";

                $str .= $this->dia."\n";

                $str .= "</a>";

                $str .= "</td>\n";
                $this->proximo_dia();
            }
            $ccol++;
            $ccol = $ccol % 7;
            $casa++;
            if ( ( $casa % 7 ) == 0 ) {
                $str .= "</tr>\n";
                $this->linhafecha = true;
            }
        }
        while( $ccol != 0 ) {
            $ccol++;
            $ccol = $ccol % 7;
            $str .= "<td>&nbsp;</td>\n";
        }
        $str .= "</tr>\n";

        $str .= "<tfoot>";
        $str .= "<tr>";
        $str .= "<th colspan=7 class='".self::HOJE_CSS_CLASS."'>";


        $str .= "<a title='Hoje' href='$url-hoje'>HOJE</a>";
        $str .= "</th>";
        $str .= "</tfoot>";

        $str .= "</table>\n";

        return $str;

    }



}

?>
