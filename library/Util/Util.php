<?php
/**
 * UTIL - Métodos uteis para o desenvolvimento
 *
 * Conjunto de métodos que seguem boas práticas no php para faciltar e organizar o desenvolvimento
 *
 *
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version v 1.0.1 04/07/2008
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category UTIL
 *
 *
 *
 */
class Util {

    public function __construct() {
    }

    public function strstr() {
         return array_shift(explode($n,$h,2));
    }

    /**
     * Mensagem de erro Padrão, ao confirmar retorna uma página
     * @param string $mensagem
     */
    public static function erroVolta($mensagem) {
        echo ("<script type='text/javascript'>alert('$mensagem');history.go(-1);</script>");
        echo $mensagem;

        exit();
    }

    /**
     * Transforma os caracteres \n e \r em <br />
     * @param string $texto
     * @return string
     */
    public static function quebraLinha($texto) {
        $formatado = str_replace("\r\n", "<br />", $texto);
        return $formatado;
    }

    /**
     * Filtra competamente uma string usando a classe filter
     *
     * @param string $string
     * @return string
     */
    public static function limparString($string) {
        $string = str_replace("\r\n", "", $string);
        $string = filter_var($string, FILTER_SANITIZE_SPECIAL_CHARS);
        $string = filter_var($string, FILTER_SANITIZE_STRING);
        return $string;
    }

    /**
     * Transforma quebras de linha html <br /> em \r\n
     * @param string $texto
     * @return string
     */
    public static function voltaQuebraLinha($texto) {
        $formatado = str_replace("<br />", "\r\n", $texto);
        $formatado = str_replace("<br>", "\r\n", $formatado);
        return $formatado;
    }

    /**
     * Retorna a data e hora no formato internaciona aaaa-mm-dd hh:mm:ss
     * @return string
     */
    public static function dateTime() {
        return date("Y-m-d H:i:s");
    }

    /**
     * Recebe a data para o formato: aaaa-mm-dd retorna dd-mm-aaaa
     * @param string $strData
     * @return string
     */
    public static function converterData($strData, $separador = "/") {
        $strData = substr($strData, 0, 10);
        $strDataFinal = "";
        if(preg_match("#-#", $strData) == 1) {
            $strDataFinal .= implode(
                    $separador,
                    array_reverse(explode('-', $strData)));
        }
        return $strDataFinal;
    }

    /**
     * Converte uma data Brasileira para o formato internacional <br />
     * dd-mm-aaaa para aaaa-mm-dd <br />
     * @param string $strData
     * @param string $separador
     * @return string
     */
    public static function convertBrazilDateToInternational($strData,$separador = "-") {
        $dia = substr($strData, 0, 2);
        $mes = substr($strData, 3, 2);
        $ano = substr($strData, 6, 4);
        return $ano . $separador . $mes . $separador . $dia;
    }

    /**
     * Faz o tratamento de dados de entrada para evitar Sql Injects. Se $sql for um array (um $_POST ou $_GET
     * por exemplo) ele retornará o mesmo array tratando todos os campo. Se for uma string retorna a string
     * tratada
     * @param string $sql array ou var
     * @param boolean $addSlashes
     * @return string
     */
    /**
     * Faz o tratamento de dados de entrada para evitar Sql Injects. Se $sql for um array (um $_POST ou $_GET
     * por exemplo) ele retornará o mesmo array tratando todos os campo. Se for uma string retorna a string
     * tratada
     * @param string $sql array ou var
     * @param boolean $addSlashes
     * @return string
     */
    public static function antInject($sql, $addSlashes = false, $sanitize = false) {
        if(is_array($sql)) {
            $ai = new ArrayIterator($sql);
            while ($ai->valid()) {
                @$sql[$ai->key()] = preg_replace(
                        sql_regcase(
                        "/(from|select|insert|deletwhere|drop table|show tables|#|\*|--|\\\\)/"),
                        "",
                        $sql[$ai->key()]);
                @$sql[$ai->key()] = trim($sql[$ai->key()]); //limpa espaços vazio
                //$sql[$ai->key()] = str_replace('"', "'", $sql[$ai->key()]);
                if($addSlashes)
                    $sql[$ai->key()] = addslashes($sql[$ai->key()]);
                if($sanitize)
                    $sql[$ai->key()] = strip_tags($sql[$ai->key()]); //tira tags html e php
                $ai->next();
            }
            if($sanitize)
                $sql = filter_var_array($sql, FILTER_SANITIZE_STRING);
        } else {
            $sql = preg_replace(
                    sql_regcase(
                    "/(from|select|insert|deletwhere|drop table|show tables|#|\*|--|\\\\)/"),
                    "",
                    $sql);
            $sql = trim($sql); //limpa espaços vazio
            if($addSlashes)
                $sql = addslashes($sql); //Adiciona barras invertidas a uma string
            if($sanitize) {
                $sql = filter_var($sql, FILTER_SANITIZE_STRING);
                $sql = strip_tags($sql); //tira tags html e php
            }
        }
        return $sql;
    }

    /**
     * Inicia uma Sessão
     * @param int $duracao
     * @param string $cacheLimiter public - private - nocache - private_no_expire
     */
    public static function iniciarSessao($duracao = 15, $cacheLimiter = "public") {
        if(! isset($_SESSION)) {
            session_cache_expire($duracao);
            session_cache_limiter($cacheLimiter);
            session_start();
        }
    }

    /**
     * Destroi uma cessão
     */
    public static function destruirSessao() {
        Samus::sessionStart();
        $_SESSION = array();
        session_unset();
        session_destroy();
    }

    /**
     * Redireciona para uma página no tempo específicado sem usar Headers
     * @param string $url
     * @param int $tempo em segundos
     */
    public static function redirect($url, $tempo = 0 , $newWindow = false) {
        if($newWindow) {
            echo "<meta http-equiv=\"refresh\" content=\"$tempo;URL=javascript:window.open('$url','_blank');\" />";
        } else {
            echo "<meta http-equiv='refresh' content='$tempo;URL=$url' />";
        }
    }



    /**
     * Valida um email qualquer com API propia do PHP
     * @param string $email
     * @return boolean
     */
    public static function validarEmail($email) {
        $resultado = filter_var($email, FILTER_VALIDATE_EMAIL);
        if($resultado)
            return true;
        else
            return false;
    }

    /**
     * Exibe um botão de Voltar, com imagem ou texto.
     * Caso a url seja especificada como "true" ou como "js" o destino do botão
     * é a página anterior, se estiver vazia volta para mesma página,
     * @param string|boolean $url
     * @param string $btnImage
     */
    public static function exibirVoltar($url = '',
            $btnImage = "imagens/voltar.png") {
        if($url == 'js')
            $url = "javascript:history.go(-1)";
        if(empty($url))
            $url = $_SERVER['PHP_SELF'];
        if(! empty($btnImage))
            echo "<a href='$url'><img src='$btnImage' alt='$url' /></a>";
        else
            echo "<a href='$url'>Voltar</a>";
    }

    /**
     * Retorna o tipo de arquivo a patir do nome do arquivo
     * @param string $filename
     * @return string
     */
    public static function getFileTypeByFileName($filename) {
        $tipo = explode(".", $filename);
        $tipo = $tipo[count($tipo) - 1];
        return $tipo;
    }

    public static function locationButton($nome, $href, $classCss = '') {
        if(func_num_args() > 3) {
            $parametrosAdicionais = func_get_arg(4);
        }
        echo "<input type='submit' value='$nome' class='$classCss' id='$nome' onclick='javascript: document.location.href=" . '"' . $href . '"' . "' onmouseover='this.style.cursor=" . '"' . "pointer" . '"' . "' $parametrosAdicionais />";
    }

    /**
     * Retorna o endereço do arquivo atual SEM qualquer variavel passada por $_GET
     * ou outra coisa que tiver na url depois do nome do arquivo
     * @return string
     */
    public static function phpSelf() {
        $url = $_SERVER['PHP_SELF'];
        $pos = strpos($url, ".php");
        $url = substr($url, 0, $pos);
        return $url . '.php';
    }


    /**
     * Calcula a idade a partir da data de nascimento, a data de nascimento pode ser
     * no formato internacional aaaa-mm-dd ou no formato brasileiro dd-mm-aaaa <br />
     * O metodo retorna uma array com a seguinte estrutura: <br />
     * $idade["anos"] <br />
     * $idade["meses"] <br />
     * $idade["dias"] <br />
     * <br />
     *
     * @param string $nascimento data de nascimento dd-mm-aaaa ou aaaa-mm-dd
     * @param string $separador separador de itens da data
     * @return int[]
     */
    public static function calcularIdade($nascimento, $separador = "-") {
        if(substr($nascimento, 4, 1) == $separador) {
            $nascimento = self::converterData($nascimento, $separador);
        }
        $hoje = date("d-m-Y");
        $aniv = explode($separador, $nascimento);
        $atual = explode("-", $hoje);
        $idadeArray = array();
        $idade = $atual[2] - $aniv[2];
        if($aniv[1] < $atual[1])
            $idadeArray["meses"] = $atual[1] - $aniv[1];
        else
            $idadeArray["meses"] = $atual[1];
        if($aniv[0] < $atual[0])
            $idadeArray["dias"] = $atual[0] - $aniv[0];
        else
            $idadeArray["dias"] = $atual[0];
        if($aniv[1] > $atual[1]) {
            $idade --;
        } elseif($aniv[1] == $atual[1] && $aniv[0] > $atual[0]) {
            $idade --;
        }
        $idadeArray["anos"] = $idade;
        return $idadeArray;
    }


    /**
     * Calcula o intervalo entre duas datas no formato ISO, o intervalo é dado
     * no formato específicado em intevalor q pode ser
     * y - ano
     * m - meses
     * d - dias
     * h - horas
     * n - minutos
     * default ´se gundos
     *
     * @param string $data1
     * @param string $data2
     * @param string $intervalo m, d, h, n,y
     * @return int|string intervalo de horas
     */
    public static function dataDif($data1, $data2, $intervalo) {

        switch ($intervalo) {
            case 'y':
                $Q = 86400*365;
                break; //ano
            case 'm':
                $Q = 2592000;
                break; //mes
            case 'd':
                $Q = 86400;
                break; //dia
            case 'h':
                $Q = 3600;
                break; //hora
            case 'n':
                $Q = 60;
                break; //minuto
            default:
                $Q = 1;
                break; //segundo
        }

        return round((strtotime($data2) - strtotime($data1)) / $Q);
    }


    /**
     * Obtem o IP do usuário 
     * @return string
     */
    public static function getIpUser() {
        return getenv("REMOTE_ADDR");
    }


    /**
     * Retorna uma chamada para uma função javaScript incluindo as tags
     * <script>
     * @param string $functionName
     * @param array $params
     * @return string
     */
    public static function callJsFunction($functionName ,$param1="") {

        $str = '<script type="text/javascript">';
        $str .= $functionName.'(';

        foreach(func_get_args() as $key => $p) {
            if($key != 0) {
                if(is_numeric($p)) {
                    $str .= "$p,";
                } else {
                    $str .= "'$p',";
                }
            }
        }

        $str = substr($str, 0 , -1);

        $str .= ');';
        $str .= "</script>";

        return $str;
    }
    
    /**
     *  Define se uma variável está ou não vazia conforme as determinações do
     * framework
     *
     * @param mixed $var
     * @return boolean
     */
    public static function isEmpty($var = null) {

        if (! isset($var)) { // se ela não estive sid iniciada
            return true;
        }

        $emptyCases = array ("", null, array () ); //casos que serão tratados como vazio

        foreach ( $emptyCases as $emp ) { // loop para verificar se esta realmente vazio
            if ($var === $emp) {
                return true;
            }
        }

        return false;
    }

}
?>
