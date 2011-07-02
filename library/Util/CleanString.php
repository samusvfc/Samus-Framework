<?php

/**
 * Description of Util_CleanString
 *
 * @author Vinicius Fiorio - samusdev@gmail.com.br
 * @package util
 */
class Util_CleanString {

    /**
     * Array com os termos que serгo substituidos
     * @var array
     */
    private static $removeArray = array(
        " " => "_",
        "a" => "a",
        "A" => "A",
        "b" => "b",
        "B" => "B",
        "c" => "c",
        "C" => "C",
        "d" => "d",
        "D" => "D",
        "e" => "e",
        "E" => "E",
        "f" => "f",
        "F" => "F",
        "g" => "g",
        "G" => "G",
        "h" => "h",
        "H" => "H",
        "i" => "i",
        "I" => "I",
        "j" => "j",
        "J" => "J",
        "k" => "k",
        "K" => "K",
        "l" => "l",
        "L" => "L",
        "m" => "m",
        "M" => "M",
        "n" => "n",
        "N" => "N",
        "o" => "o",
        "O" => "O",
        "p" => "p",
        "P" => "P",
        "q" => "q",
        "Q" => "Q",
        "r" => "r",
        "R" => "R",
        "s" => "s",
        "S" => "S",
        "t" => "t",
        "T" => "T",
        "u" => "u",
        "U" => "U",
        "v" => "v",
        "V" => "V",
        "x" => "x",
        "X" => "X",
        "y" => "y",
        "Y" => "Y",
        "W" => "W",
        "z" => "z",
        "Z" => "Z",
        "б" => "a",
        "Б" => "A",
        "й" => "e",
        "Й" => "E",
        "н" => "i",
        "Н" => "I",
        "у" => "o",
        "У" => "O",
        "ъ" => "u",
        "Ъ" => "U",
        "а" => "a",
        "А" => "A",
        "и" => "e",
        "И" => "E",
        "м" => "i",
        "М" => "I",
        "т" => "o",
        "Т" => "O",
        "щ" => "щ",
        "Щ" => "U",
        "г" => "a",
        "Г" => "A",
        "х" => "o",
        "Х" => "O",
        "в" => "a",
        "В" => "A",
        "к" => "e",
        "К" => "E",
        "о" => "i",
        "О" => "I",
        "ф" => "o",
        "Ф" => "O",
        "ы" => "u",
        "Ы" => "U",
        "," => "",
        "!" => "",
        "#" => "",
        "%" => "",
        "¬" => "",
        "-" => "_",
        "{" => "",
        "}" => "",
        "^" => "",
        "ґ" => "",
        "`" => "",
        "\\" => "",
        "/" => "",
        ";" => "",
        ":" => "",
        "?" => "",
        "№" => "1",
        "І" => "2",
        "і" => "3",
        "Є" => "a",
        "є" => "o",
        "з" => "c",
        "З" => "c",
        "ь" => "u",
        "Ь", "U",
        "д" => "a",
        "Д", "A",
        "п" => "i",
        "П", "I",
        "ц" => "o",
        "Ц", "O",
        "л" => "e",
        "Л", "E",
        "$" => "s",
        "я" => "y",
        "w" => "w",
        "<" => "",
        ">" => "",
        "[" => "",
        "]" => "",
        "&" => "e",
        "'" => '',
        '"' => "",
        '1' => '1',
        '2' => '2',
        '3' => '3',
        '4' => '4',
        '5' => '5',
        '6' => '6',
        '7' => '7',
        '8' => '8',
        '9' => '9',
        '0' => '0',
        '_' => '_'
    );
    private static $acentosArray = array(
        'б' => 'a', 'Б' => 'A',
        'й' => 'e', 'Й' => 'E',
        'н' => 'i', 'Н' => 'i',
        'у' => 'o', 'У' => 'O',
        'ъ' => 'u', 'Ъ' => 'U',
        'в' => 'в', 'в' => 'в',
        'к' => 'к', 'К' => 'в',
        'ф' => 'ф', 'Ф' => 'в',
        'а' => 'a', 'А' => 'в',
        'з' => 'c', 'З' => 'C',
        'г' => 'a', 'Г' => 'г',
        'х' => 'o', 'Х' => 'o'
    );

    /**
     * Limpa uma string para ser usada como termo de uma URL
     * @param string $string
     * @return string
     */
    public static function clean($string, $caseSensitive=false) {
        $finalString = "";

        if (!$caseSensitive) {
            $string = strtolower($string);
        }

        $string = str_replace("'", "", $string);
        $string = str_replace('"', "", $string);

        $string = trim($string);

        $string = filter_var($string, FILTER_SANITIZE_STRING);

        foreach (str_split($string) as $str) {
            if (array_key_exists($str, self::$removeArray)) {
                $finalString .= self::$removeArray[$str];
            }
        }

        $finalString = str_replace("__", "_", $finalString);
        $finalString = str_replace("__", "_", $finalString);

        if (substr($finalString, -1, 1) == "_") {
            $finalString = substr($finalString, 0, -1);
        }

        return $finalString;
    }

    /**
     * Remove os acentos de uma string
     *
     * @param string $string
     * @return string
     */
    public static function removeAcento($string) {
        $finalString = "";
        $string = str_replace("'", "", $string);
        $string = str_replace('"', "", $string);
        $string = str_replace('&', "", $string);

        $string = trim($string);

        $string = filter_var($string, FILTER_SANITIZE_STRING);

        foreach (str_split($string) as $str) {
            if (key_exists($str, self::$acentosArray)) {
                $finalString .= self::$acentosArray[$str];
            } else {
                $finalString .= $str;
            }
        }

        if (substr($finalString, -1, 1) == "_") {
            $finalString = substr($finalString, 0, -1);
        }

        return $finalString;
    }

    /**
     *
     * @param string $string
     * @return string
     */
    public static function quotes($string) {
        return addslashes(str_replace('\\', '', $string));
    }


    /**
     * Remoзгo segura das barras 
     * @param string $string
     * @return string
     */
    public static function stripSlashes($string) {
        if (!empty($string))
            return stripslashes (str_replace('\\', '', $string));
        else
            return "";
    }


    /** Create a friendly URL (slug)
     * @param string in UTF-8 encoding
     * @return string containing only a-z0-9_-
     * @uses iconv
     * @copyright Jakub Vrana, http://www.vrana.cz/
     */
    function friendlyUrl($title) {
        $url = $title;
        $url = preg_replace('~[^\\pL0-9_]+~u', '-', $url);
        $url = trim($url, "-");
        $url = iconv("utf-8", "us-ascii//TRANSLIT", $url);
        $url = strtolower($url);
        $url = preg_replace('~[^-a-z0-9_]+~', '', $url);
        return $url;
    }

}

?>
