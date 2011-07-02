<?php
/**
 * Obtem uma instancia unica de uma classe
 *
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version v 1.0.1
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category CRUD
 * @package CRUD
 */
class Samus_CRUD_Singleton {
    private static $instances = array ();

    private function __construct() {
    }

    /**
     * Retorna uma intancia unica de um objeto, os parametros do construtor devem
     * ser passados como parametros adicionais após o nome da classe
     *
     * @param string $class nome da classe
     * @param mixed optional construc value1
     * @param mixed optional construc value1
     * @param int|string $key uma chave para quando precisar de ter intancias
     * @return mixed
     */
    public static function getInstance($class , $key=null) {
        if (is_null ( $class )) {
            trigger_error ( "Informe a classe", E_USER_ERROR );
        }
        if (! array_key_exists ( $class.$key, self::$instances )) {
            $strEval = 'self::$instances ["'.$class.$key.'"] = new '.$class.' (';

            $args = func_get_args();

            array_shift($args);
            array_shift($args);

            if (count($args) > 0) {
                $i=0;
                foreach ($args as $arg) {
                    $strEval .=  '$args['.$i.'],';
                    ++$i;
                }
                $strEval = substr ( $strEval, 0, - 1 );
            }

            $strEval .= ");";
            eval ( $strEval );

        }

        return self::$instances [$class.$key];
    }

    public static function getClassesInstances() {
        return self::$instances;
    }

    public final function __clone() {
        trigger_error ( "Cannot clone instance of Samus_CRUD_Singleton pattern", E_USER_ERROR );
    }
}

?>