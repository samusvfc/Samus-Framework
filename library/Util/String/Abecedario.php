<?php
/**
 * Classe Util_String_Abecedario.class
 *
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version 1.0 20/08/2008
 * @package util/string
 */
class Util_String_Abecedario {

    /**
     * Array com todos os caracteres
     * @var string[]
     */
    private static $caracteres = array("a" , "b" , "c" , "d" , "e" , "f" , "g" , "h" , "i" , "j" , "k" , "l" , "m" , "n" , "o" , "p" , "q" , "r" , "s" , "t" , "u" , "v" , "x" , "y" , "z");

    public function __construct() {

    }

    public function __tostring() {
        return $this->getCaracteres();
    }

    public static function caractersLink($letterVar="letter" , $url="") {
        for ($i=65;$i<91;$i++) {
            printf('<a href="%s?%s=%s">%s</a>&nbsp;', $url,$letterVar, chr($i), chr($i));
        }
    }



    /**
     * @return string[]
     */
    public static function getCaracteres() {
        return self::$caracteres;
    }

    public function setCaracteres(array $caracteres) {
        self::$caracteres = $caracteres;
    }
}
?>
