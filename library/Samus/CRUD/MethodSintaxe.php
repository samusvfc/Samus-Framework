<?php

/**
 * Method Sintaxe
 * Define a forma de escrita dos métodos se camelCase for setado para true os 
 * métodos deverão ser escritos no formato
 * 
 * setPropertieName($propertieName); { } 
 * getPropertieName(); { }
 * 
 * 
 * Se for setado para false
 * set_propertie_name($propertie_name); { }
 * get_propertie_name(); { }
 * 
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version v 1.0.1
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category CRUD
 * @package CRUD
 *
 */
class Samus_CRUD_MethodSintaxe {

    /**
     * Define se os métodos usam a sintaxe camelCase ou underline
     * @var boolean
     */
    public static $camelCase = true;

    /**
     * Define o tipo de escrita dos métodos para camelCase ou não
     * camelCase usa maiusculas entre os nomes, se falso deve ser colocado um 
     * underline entre as palavras
     * @param $isCamelCase 
     */
    public static function setMethodSintaxe($isCamelCase=true) {
        self::$camelCase = $isCamelCase;
    }

    /**
     * Obtem o nome do setter da propiedade especificada
     * @param string $propertieName
     * @return string
     */
    public static function buildSetterName($propertieName) {
        if (self::$camelCase) {
            return "set" . ucfirst($propertieName);
        } else {
            return 'set_' . $propertieName;
        }
    }

    /**
     * Obtem o nome do getter da propriedade especificada
     * @param string $propertieName
     * @return string
     */
    public static function buildGetterName($propertieName) {
        if (self::$camelCase) {
            return "get" . ucfirst($propertieName);
        } else {
            return 'get_' . $propertieName;
        }
    }

}
