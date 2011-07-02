<?php
/**
 * Agrupa métodos uteis para manipulação de strings
 */
class Util_String {

/**
 * Converte os caracteres maiusculos em minusculos adicionando um underline
 * como espaço entre as palavras (o underline pode ser substituido caso o
 * divisor seja alterado)
 *
 * @param string $string string que deve ser convertida
 * @param string $divisor divisor entre as palavras
 * @return string string formatada
 */
    public static function upperToUnderline($string , $divisor="_") {
        $string = strtolower($string{0}).substr($string,1);
        foreach(str_split($string) as $caracter) {
            if($caracter != $divisor)
                $string = str_replace(strtoupper($caracter) , $divisor.strtolower($caracter) , $string);
        }
        return $string;
    }

    /**
     * Substitui a notação camelCase para separação com espaços
     * @param string $string
     * @return string
     */
    public static function upperToSpace($string) {
        return self::upperToUnderline($string," ");
    }

    /**
     * Converte a nomenclatura de underlines para nomenclatura de letra maiuscula
     * para cada palavra da expressão
     *
     * @param string $string
     * @param string $divisor
     * @return string
     */
    public static function underlineToUpper($string , $divisor="_") {
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

        return $string;
    }

    /**
     * Converte a nomenclatura de underlines para nomenclatura legivel colocando
     * espaços no lugar das underline
     *
     * @param string $string
     * @param string $divisor
     * @return string
     */
    public static function underlineToSpace($string , $divisor="_") {
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

        return $string;
    }


    /**
     * Adiciona 0 (zeros) no inicio da string para formatação até a largura máxima
     * estipulada
     *
     * @param string $string
     * @param int $limitSize
     * @return string
     */
    public static function addZerosToString($string , $limitSize) {
        while (strlen($string) < $limitSize) {
            $string = "0".$string;
        }
        return $string;
    }


    /**
     * Find first occurrence of a string
     * @param string $haystack string de origem
     * @param string $needle string para buscar na origem
     * @param boolean $before_needle se irá retornar tudo que esta antes da string
     * @return string
     */
    public static function strstr($haystack, $needle, $before_needle=FALSE) {
    //Find position of $needle or abort
        if($before_needle) {
            return substr(strrev(strstr(strrev($haystack), strrev($needle))), 0, -strlen($needle));
        } else {
            return strstr($haystack, $needle);
        }

    }

    /**
     * Diminui uma string considerando os espaços e não parando no meio das palvras
     * É baseaado no modificador |truncate do smarty
     *
     * @param string $string String que será tratada
     * @param int $length comprimento máximo da string
     * @param string $etc
     * @param boolean $break_words
     * @param boolean $middle 
     * @return string
     */
    public static function truncate($string, $length = 80, $etc = '...', $break_words = false, $middle = false) {
        if ($length == 0)
            return '';

        if (strlen($string) > $length) {
            $length -= min($length, strlen($etc));
            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '', substr($string, 0, $length+1));
            }
            if(!$middle) {
                return substr($string, 0, $length) . $etc;
            } else {
                return substr($string, 0, $length/2) . $etc . substr($string, -$length/2);
            }
        } else {
            return $string;
        }
    }

    /**
     * Decodifica um Array
     * @return array
     */
    public static function utf8ArrayDecode($array) {

        $return = array();

        foreach ($array as $key => $val) {
            if( is_array($val) ) {
                $return[$key] = self::utf8ArrayDecode($val);
            }
            else {
                $return[$key] = utf8_decode($val);
            }
        }
        return $return;

    }


}


?>