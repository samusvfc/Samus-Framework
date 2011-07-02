<?php

/**
 * Classe responsável pela realização das Querys do Samus_CRUD, onde é possível selecionar
 * o modo de execução das querys, através do PDO ou MySqli
 *
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version 0.1
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category Samus_CRUD
 * @package Samus_CRUD
 */
class Samus_CRUD_CRUDQuery {

    /**
     * Modo atual de execução das querys
     * @var string
     */
    private static $modo = "pdo";
    /**
     * PDo ou resource responsável pela execução das querys
     * @var PDO|resource
     */
    private static $conn;

    const QUERY_MODE_MYSQLI = "mysqli";

    const QUERY_MODE_PDO = "pdo";

    public function __construct() {
        
    }

    /**
     * Executa uma query conforme o modo selecionado
     * @param string $sql
     * @return PDO|resource
     */
    public static function executeQuery($sql) {

        if (self::$modo == self::QUERY_MODE_PDO) {

            $r1 = self::getConn()->query(self::cleanStringToQuery($sql));

            $error = Samus_CRUD::getPDO ()->errorInfo();

            if (isset($error [1])) {
                if ($error [1] != null) {
                    throw new Samus_CRUD_CRUDQueryException("CRUD SQL ERROR - $sql <hr />ERROR INFO:" . $error [2]);
                }
            }

            return $r1;
        } elseif (self::$modo == self::QUERY_MODE_MYSQLI) {

            $r1 = mysqli_query(self::getConn (), $sql);

            if (mysqli_errno(self::getConn ()) != 0) {
                throw new Samus_CRUD_CRUDQueryException("CRUD SQL ERROR - $sql <hr />ERROR INFO:" . mysqli_errno(Samus_Database_ConnectionMySqli::getConn ()));
            }

            return $r1;
        }
    }

    public static function cleanStringToQuery($sql) {
        return str_replace('\\\\', '\\', $sql);
    }

    /**
     * Executa uma query e retorna uma matriz associativa dos resultados
     * @param string $sql
     */
    public static function query($sql) {

        if (self::getModo () == self::QUERY_MODE_PDO) {

            $pdo = self::executeQuery($sql);

            $result = null;

            try {
                $result = $pdo->fetchAll(PDO::FETCH_ASSOC);
            } catch (PDOException $ex) {
                $result = null;
            }

            return $result;
        } elseif (self::getModo () == self::QUERY_MODE_MYSQLI) {

            $r1 = self::executeQuery($sql);

            $result = null;

            $aAux = array();
            while ($v1 = mysqli_fetch_assoc($r1)) {
                $aAux [] = $v1;
            }

            return $aAux;
        }
    }

    /**
     * Obtem o ultimo ID inserido independente do tipo de query
     * @return int
     */
    public static function lastInsertId() {
        if (Samus_CRUD_CRUDQuery::isPDOMode ()) {
            return self::$conn->lastInsertId();
        } elseif (Samus_CRUD_CRUDQuery::isMySqliMode ()) {
            return mysqli_insert_id(Samus_CRUD_CRUDQuery::getConn ());
        }
    }

    /**
     * @return int
     */
    public static function getModo() {
        return self::$modo;
    }

    /**
     * Define o modo de excução das querys
     * @param int $modo
     */
    public static function setModo($modo = 1) {
        if ($modo != self::QUERY_MODE_MYSQLI && $modo != self::QUERY_MODE_PDO)
            $modo = self::QUERY_MODE_PDO;
        self::$modo = $modo;
    }

    public static function getConn() {
        return self::$conn;
    }

    public static function setConn($conn) {
        self::$conn = $conn;
    }

    /**
     * Testa se o query mode é PDO
     * @return boolean
     */
    public static function isPDOMode() {
        if (self::getModo () == self::QUERY_MODE_PDO) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Testa se o modo de execução de query é o Mysqli
     * @return boolean
     */
    public static function isMySqliMode() {
        if (self::getModo () == self::QUERY_MODE_MYSQLI) {
            return true;
        } else {
            return false;
        }
    }

}

?>