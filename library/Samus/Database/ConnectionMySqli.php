<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
*/

/**
 * Description of Samus_Database_ConnectionMySqli
 *
 * @author samus
 */
class Samus_Database_ConnectionMySqli {

    /**
     * Testa se o banco já esta conectado
     * @var boolean
     */
    private static $conectado = false;

    /**
     * Nome do banco de dados
     *
     * @var string
     */
    private static $databaseName = '';

    /**
     * Usuário
     *
     * @var string
     */
    private static $user = '';

    /**
     * Senha
     *
     * @var string
     */
    private static $password = '';

    /**
     * Host do banco (default: localhost)
     * @var string
     */
    private static $host = '';

    /**
     * Resource da conexão atual
     * @var resource
     */
    public static $resource;

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

    public function Conexao() {

    }

    /**
     * Realiza a conexão com o banco, caso qualque uma das informações de
     * conexão não tenham sido informadas o valor assumido será o das váriaveis
     * estáticas da classe.
     *
     * @param string $databaseName
     * @param string $user
     * @param string $password
     * @param string $host
     */
    public static function connect($databaseName = '', $user = '', $password = '', $host = '' , $charset='latin1' , $engine='InnoDB' ) {


        self::$databaseName = $databaseName;
        self::$user = $user;
        self::$password = $password;
        self::$host = $host;
        self::$charset = $charset;
        self::$engine = $engine;


        if(!($con = mysqli_connect(self::$host,self::$user, self::$password))) {
            echo ("Erro ao se conectar com o banco de dados");
            exit();
        }
        if(! (mysqli_select_db($con , self::$databaseName))) {

            try {
                mysqli_query($con , "CREATE DATABASE `".self::$databaseName."`;");
                echo mysql_error();
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }

            if(! (mysqli_select_db($con , self::$databaseName))) {
                echo ("Erro ao se conectar com o banco especificado");
                exit();
            }
        }

        self::$resource = $con;
    }

    public static function conectarConfigFile() {
        $xml = simplexml_load_file(PROJECT_CONFIG_FILE);

        self::connect(
                $xml->database->name ,
                $xml->database->user ,
                $xml->database->password ,
                $xml->database->host ,
                $xml->database->charset ,
                $xml->database->engine
        );
    }


    /**
     * Encerra uma conexão do banco
     */
    public static function desconectar() {
        mysqli_close(self::getConn());
        self::$conectado = false;
    }

    /**
     * Testa se o banco esta conectado
     *
     * @return boolean
     */
    public function testarConexao() { //retorna true caso ja esteja conectado
        return self::$conectado;
    }

    /**
     * Obtem o resource da conexão
     *
     * @return mixed mysql resource
     */
    public function getResource() {
        return $this->resource;
    }

    /**
     * Obtem o engine
     *
     * @return string
     */
    public static function getEngine() {
        return self::$engine;
    }

    /**
     * Obtem o charset
     *
     * @return string
     */
    public static function getCharset() {
        return self::$charset;
    }

    /**
     * Obtem o nome padrão do banco
     *
     * @return banco
     */
    public static function getDatabaseName() {
        return self::$databaseName;
    }

    /**
     * Seta o nome do banco
     *
     * @param string $banco
     */
    public function setDatabaseName($banco) {
        $this->databaseName = $banco;
    }

    /**
     * Obtem o resource da conexção
     * @return resource
     */
    public static function getConn() {
        return self::$resource;
    }

    public static function close() {
        mysqli_close(self::getConn());
    }

}
?>
