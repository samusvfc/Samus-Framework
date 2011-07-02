<?php

/**
 * @author Vinicius Fiorio - samusdev@gmail.com
 */
class Samus_Database_Connection {

    /**
     * Usuário do banco de dados
     * @var string
     */
    private static $user;

    /**
     * Seba do banco de dados
     * @var string
     */
    private static $password;

    /**
     * Host
     * @var string
     */
    private static $host = "localhost";

    /**
     * Adapter
     * @var string
     */
    private static $adapter = "mysql";

    /**
     * Define o banco de dados
     * @var string
     */
    private static $databaseName;

    /**
     * Objeto PDO que realizara as operações
     * @var PDO
     */
    private static $pdo;

    /**
     * Engine utilizado
     * @var string
     */
    private static $engine = 'InnoDB';

    /**
     * Char set default
     * @var string
     */
    private static $charset = 'latin1';

    public function __construct() {

    }

    public static function connect($adapter = "", $host = "", $dataBaseName = "", $user = "", $password = "" ) {

        if (! empty($adapter))
        self::setAdapter($adapter);

        if (! empty($host))
        self::setHost($host);

        if (! empty($dataBaseName))
        self::setDatabaseName($dataBaseName);

        if (! empty($user))
        self::setUser($user);

        if (! empty($password))
        self::setPassword($password);


        self::$pdo = new PDO(self::getAdapter().":host=".self::getHost().";dbname=".self::getDatabaseName() , self::getUser() , self::getPassword());

   


    }

    public static function close() {
        self::$pdo = null;
    }

    /**
     * @return string
     */
    public static function getAdapter() {
        return self::$adapter;
    }

    /**
     * @return string
     */
    public static function getDatabaseName() {
        return self::$databaseName;
    }

    /**
     * @return string
     */
    public static function getHost() {
        return self::$host;
    }

    /**
     * @return string
     */
    public static function getPassword() {
        return self::$password;
    }

    /**
     * @return PDO
     */
    public static function getPdo() {
        return self::$pdo;
    }

    /**
     * @return string
     */
    public static function getUser() {
        return self::$user;
    }

    /**
     * @param string $adapter
     */
    public static function setAdapter($adapter) {
        self::$adapter = $adapter;
    }

    /**
     * @param string $databaseName
     */
    public static function setDatabaseName($databaseName) {
        self::$databaseName = $databaseName;
    }

    /**
     * @param string $host
     */
    public static function setHost($host) {
        self::$host = $host;
    }

    /**
     * @param string $password
     */
    public static function setPassword($password) {
        self::$password = $password;
    }

    /**
     * @param PDO $pdo
     */
    public static function setPdo($pdo) {
        self::$pdo = $pdo;
    }

    /**
     * @param string $user
     */
    public static function setUser($user) {
        self::$user = $user;
    }
	/**
     * @param $charset the $charset to set
     */
    public static function setCharset($charset) {
        Samus_Database_Connection::$charset = $charset;
    }

	/**
     * @param $engine the $engine to set
     */
    public static function setEngine($engine) {
        Samus_Database_Connection::$engine = $engine;
    }

	/**
     * @return the $charset
     */
    public static function getCharset() {
        return Samus_Database_Connection::$charset;
    }

	/**
     * @return the $engine
     */
    public static function getEngine() {
        return Samus_Database_Connection::$engine;
    }

    public static function getConn() {
        return self::$pdo;
    }


}
?>
