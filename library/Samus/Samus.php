<?php

/**
 * Samus Framework
 *
 *
 * @author Vinicius Fiorio Cust�dio - Samusdev@gmail.com
 * @package Samus
 * @todo
 * Criar o m�todo de gera��o de Admins
 * Adaptar as classes de gera��o de Admins para o padr�o do Samus
 * Criar uma forma para gera��o de classses associativas
 * Melhorar a forma que a modelo resolve atributos multivalorados
 * Criar uma rotina que atualize e crie todas as tabelas de todos os modelos que estiverem na pasta
 */
class Samus {

    /**
     * Define uma conex�o com o banco, as chaves abaixo devem ser especificadas
     * <ul>
     * <li>NAME -> nome da tabela do banco de dados</li>
     * <li>USER    -> usupario da base</li>
     * <li>PASSWORD   -> senha</li>
     * <li>ENGINE    -> engine, defaut InnoDB</li>
     * <li>CHARSET    -> CHARSET, default latin1</li>
     * <li>TABLE_PREFIX    -> prefixo do nome das tabelasm usado para cria�ao e carregamento</li>
     * <li>HOST</li> -> Host ou ip do banco
     * </ul>
     * @var array
     * @static
     */
    private static $connection = array("NAME" => '', "USER" => '', "PASSWORD" => '', 'ENGINE' => '', 'CHARSET' => '', 'TABLE_PREFIX' => '', 'HOST' => '', 'ADAPTER' => '');

    /**
     * Diretorio dos modelos
     * @var string
     */
    const MODELS_DIR = "library/Model";

    /**
     * Diret�rio com as vis�es compiladas
     * @var string
     */
    const COMPILED_VIEWS_DIR = "../system/views_c";

    /**
     * Diret�rio padr�o dos arquivos javaScript
     * @var string
     */
    const JAVA_SCRIPT_DIR = "scripts";

    /**
     * Extens�o dos arquivos de vis�o (defalt: .html)
     * @var string
     */
    const VIEWS_FILE_EXTENSION = '.html';

    /**
     * Extens�o dos arquivos de vis�o CSS (default: .tpl.css)
     * @var string
     *
     */
    const CSS_FILE_EXTENSION = ".tpl.css";

    /**
     * Extens�o dos arquivos de vis�o JavaScript (default: .tpl.js)
     * @var string
     */
    const JS_FILE_EXTENSION = ".tpl.js";

    /**
     * Extens�o dos arquivos de Modelo (extends Samus_Model)
     * @var string
     */
    const MODELS_FILES_EXTENSION = '.php';

    /**
     * Extens�o padr�o dos Controladores,
     * @var string
     */
    const CONTROLS_FILES_EXTENSION = '.php';

    /**
     * Sufixo para o nome das classes de controle
     * @var string
     */
    const CONTROLS_CLASS_SUFIX = "_Controller";

    /**
     * Extens�o dos arkivos de include
     * @var string
     */
    const INCLUDE_FILE_EXTENSION = ".inc.php";

    /**
     * Delimitador esquerdo para supertags da views
     * @var string
     */
    const VIEWS_LEFT_DELIMITER = '${';

    /**
     * Delimitador direito para supertags das views
     * @var string
     */
    const VIEWS_RIGHT_DELIMITER = '}';

    /**
     * Prefixo para o nome das tabelas do site, todas as tabelas criadas
     * automaticamente ter�o este prefixo no nome
     * @var string
     */
    private static $tablePrefix = "";

    /**
     * Nome Default das classes de Filtro
     * @var string
     */
    const DEFAULT_FILTER_CLASS = "__Filter";

    /**
     * Define se o projeto ir� ou n�o utilizar fun��es de AutoLoad
     * @var boolean
     */
    const USE_AUTO_LOAD_FUNCTION = true;

    /**
     * Define o nome do arquivo de configura��es globais que deve estar dentro
     * do diretorio de configura��e
     * @var string
     */
    const GLOBAL_CONFIGURATION_FILE = "../system/configs/global_configuration.ini";

    /**
     * Define o diretorio da aplicacao
     * @var string
     */
    const PUBLIC_DIR = "web/public/";

    /**
     * Array de configura��es do arquivo global de configura��es
     * @var array
     */
    private static $configurationArray = array();

    /**
     * Diret�rio default dos arquivos css
     * @var string
     */
    const CSS_DIR = '_css/';

    /**
     * Diret�rio raiz dos arquivos de javascript
     * @var string
     */
    const JS_DIR = '_js/';

    public static $atualDir = "";
    /**
     * Define as linguagens para internacionaliza��o
     * @var array
     */
    public static $langs = array("pt");
    /**
     * @var string
     */
    private static $httpHost;
    /**
     * Configura o modo de execu��o das querys
     * @var string
     */
    private static $queryMode = "pdo";

    /**
     * Define o sufixo dos filtros, todas as classes de filtro devem conter o nome
     * da pasta atual seguido do nome Filter
     * @var string
     */
    const FILTER_SUFIX = "_Filter";

    /**
     * Define a string que fara a separa��o de metodos na url
     * @var string
     */
    const METHOD_URL_SEPARATOR = '.';

    /**
     * Define o separador dos parametros de um metodo de URL, o conteudo desse
     * parametro N�O pode ter strins de separa��o de variaveis (-)
     * @var string
     */
    const METHOD_URL_PARAMETER_SEPRATOR = "=";

    /**
     * Define o sufixo dos metodos acessiveis pela url, todo metodo seguido de Action
     * pode ser acessado diretamente pela url acrecentando o separador de metodos
     * seguido do nome do metod que ser� executado na classe ANTES do metodo
     * index, os metodos devem ser publicos
     * @var string
     */
    const METHOD_URL_SUFIX = "Action";
    const METHOD_TEMPLATE_SUFIX = "Template";
    const DECODE_UTF8_STRINGS = true;
    const GET_URL_VAR = 'p';
    const LAST_URL_VAR = '__var';
    /**
     * Direitorio padr�o para acesso de URL direto
     * @var string
     */
    const DEFAULT_PUBLIC_DIR = "site";
    const DEFAULT_PUBLIC_CONTROLLER = 'index';

    /**
     * Define se as variaveis enviadas por $_GET atrav�s da url da forma classica
     * s�o tratados e exibidos como v�riaveis normais
     * @var boolean
     */
    const USE_CLASSIC_GET_VARS = true;

    const SESSION_PAGE_NAME = "page";

    /**
     * Define se a sess�o j� foi iniciada
     * @var boolean
     */
    private static $sessionIsStarted = false;

    public function __construct() {

    }

    /**
     * M�todo que dispara o projeto
     * - Fax a conex�o como banco utilizando o array $connection
     * - Especifica o TablePrefix
     * - Especifica a topLevelClass
     * - Faz o tratamento da URL
     * - Exibe o tratamento chamando a URL
     *
     * @return void
     */
    public static function init() {

        if (!self::validateUrl($_GET [self::GET_URL_VAR])) {
            header($_SERVER ["SERVER_PROTOCOL"] . " 404 Not Found");
            exit ();
        }

        self::$configurationArray = parse_ini_file(self::GLOBAL_CONFIGURATION_FILE, true);

        /* if(!self::verificarConfig()) {
          echo "<meta http-equiv='refresh' content='0;URL=web/init.php'/>";
          } */
        if(!isset(self::$configurationArray['project']['directory']) || !is_dir(self::$configurationArray["project"]["directory"])) {
            echo "<h1>O projeto n�o esta corretamente configurado</h1>";
            echo "<br />Acesse o configurador de aplica��o <a href='".$_SERVER["REQUEST_URI"]."init.php"."'>".$_SERVER["REQUEST_URI"]."init.php"."</a><br /><br />";
            throw new Exception("O Projeto n�o esta corretamente configurado acesse " . $_SERVER["REQUEST_URI"]."init.php", "00");
        }

        if (!defined('WEB_DIR'))
            define('WEB_DIR', self::$configurationArray ['project'] ['directory']);

        if (!defined("WEB_URL"))
            define("WEB_URL", self::$configurationArray ['project'] ['url']);

        if (!defined("URL")) {
            define("APP_URL", self::$configurationArray ['project'] ['applUrl']);
        }

        if (!defined("SMARTY_DIR"))
            define("SMARTY_DIR", WEB_DIR . "library/Samus/View/smarty3/libs/");

        if (!defined("ADMIN_MAIL"))
            define("ADMIN_MAIL", self::$configurationArray ['project'] ['adminMail']);

        if (!defined("REQUEST"))
            define("REQUEST", $_SERVER ['REQUEST_URI']);

        if (!is_file(WEB_DIR . 'library/Samus/Samus.php')) {
            throw new Exception("O projeto n�o esta corretamente configurado: acesse o arquivo /web/init.php", 1);
            exit ();
        }

        set_include_path(WEB_DIR . 'library/Samus/View/smarty3/' . PATH_SEPARATOR . WEB_DIR . 'library/' . PATH_SEPARATOR . WEB_DIR . 'web/' . self::PUBLIC_DIR . '' . PATH_SEPARATOR . ".");
		require_once 'samus_framework_includes.php';
        
        
        /*
         * AUTO_LOAD
         * define se o m�todo m�gico para autoload de classes esta ativo
         */
        if (Samus::USE_AUTO_LOAD_FUNCTION) {
            require_once 'Util/AutoRequire.php';
            //require_once 'Samus/View/smarty3/libs/Smarty.class.php';

            function samusAutoload($className) {
                Util_AutoRequire::requireClass($className);
            }

            spl_autoload_register('samusAutoload');
        }

        ////////////////////////////////////////////////////////////////////////
        // EXIBINDO AS P�GINAS
        // aqui o fazendeiro exibe as p�ginas
        ////////////////////////////////////////////////////////////////////////

        

        if (!isset($_GET [self::GET_URL_VAR])) {
            $_GET [self::GET_URL_VAR] = "";
        }

        $samus_keeper = new Samus_Keeper($_GET [self::GET_URL_VAR]);

        $samus_keeper->displayPage();
    }

    /**
     * Verifica se o projeto foi configurado
     * @return boolean
     */
    private static function verificarConfig() {

        // se o projeto estiver marcado como configurado
        if (isset(self::$configurationArray ['project'] ['is_config']) && self::$configurationArray ['project'] ['is_config'] == 'true') {
            return true;
        } else {
            return false;
        }

        // se os campos obrigat�rios n�o estiverem preenchidos
        if (!isset(self::$configurationArray ['project'] ['directory']) || !isset(self::$configurationArray ['project'] ['url']) || !isset(self::$configurationArray ['project'] ['applUrl']) || !isset(self::$configurationArray ['project'] ['is_config'])) {
            return false;
        } else {
            // se o direitorio esta alcan�ando o um arkivo valido
            if (is_file(self::$configurationArray ['project'] ['directory']) . 'classes/samus/Samus.php') {
                return true;
            } else {
                return false;
            }
        }

        return true;
    }

    /**
     * Valida a url para conferir se n�o tem nenhuma requisi��o n�o permitida
     * @param string $url
     * @return boolean
     */
    private static function validateUrl($url) {
        $reservedExtensions = array("jpg", "jpeg", "png", "gif", "js", "css", "html");
        $a = explode('.', $url);
        $a = $a [count($a) - 1];
        foreach ($reservedExtensions as $r) {
            if ($r == $a) {
                return false;
                break;
            }
        }

        return true;
    }

    /**
     * Avalio se a URL pertence � um arquivo qualquer, se for um arquivo retorna false
     * sen�o retora true
     * @param string $url
     * @return boolean
     */
    public static function fileRule($url) {
        $a = explode('.', $url);
        $format = strtolower($a [count($a) - 1]);

        foreach (Util_Mime::getMimeTypes () as $k => $f) {
            if ($k == $format) {
                return false;
                break;
            }
        }
        return true;
    }

    public static function connect() {

        ////////////////////////////////////////////////////////////////////////
        // CONFIGURA��O DA CONEX�O
        // faz a configura��o da conex�o a partir do global_configuration.ini
        ////////////////////////////////////////////////////////////////////////


        self::setConnection(self::$configurationArray ['connection']); // l� arquivo de conex�o e realiza a conex�o


        Samus_CRUD_CRUDQuery::setModo(self::$connection ["queryMode"]);

        if (self::$connection ["queryMode"] == Samus_CRUD_CRUDQuery::QUERY_MODE_MYSQLI) {
            Samus_Database_ConnectionMySqli::connect(self::$connection ['name'], self::$connection ['user'], self::$connection ['password'], self::$connection ['host'], self::$connection ['charset'], self::$connection ['engine']);

            Samus_CRUD_CRUDQuery::setConn(Samus_Database_ConnectionMySqli::getConn ()); // essalinha ta meio redundante verificar depois
            Samus_CRUD::setConn(ConnectionMySqli::getConn ());
        } else {
            Samus_Database_Connection::connect(self::$connection ['adapter'], self::$connection ['host'], self::$connection ['name'], self::$connection ['user'], self::$connection ['password']);
            Samus_CRUD_CRUDQuery::setConn(Samus_Database_Connection::getConn ());
            Samus_CRUD::setConn(Samus_Database_Connection::getPdo ());
        }

        self::$tablePrefix = self::$connection ["table_prefix"];

        Samus_CRUD::setTablePrefix(self::getTablePrefix ());
        Samus_CRUD::setTopLevalClass("Samus_Model");
    }

    /**
     * Inicia a sess�o do projeto quando necess�ria
     */
    public static function sessionStart() {
        if (!self::$sessionIsStarted) {
            session_start ();
            session_cache_expire(30);
            Samus::$sessionIsStarted = true;
        }
    }

    /**
     * Encerra o projeto e a conex�o
     * @return void
     */
    public static function close() {

        if (!empty(self::$connection ['NAME'])) {

            if (Samus_CRUD_CRUDQuery::isPDOMode ()) {
                Samus_Database_Connection::close ();
            } elseif (Samus_CRUD_CRUDQuery::isMySqliMode ()) {
                Samus_Database_ConnectionMySqli::close ();
            }
        }
    }

    /**
     * Array de configura��es globais
     * @param array $configurationArray
     */
    public static function setConfigurationArray($configurationArray) {
        self::$configurationArray = $configurationArray;
    }

    /**
     * @return string
     */
    public static function getTablePrefix() {
        return self::$tablePrefix;
    }

    /**
     * Especifica o prefixo da tabela
     *
     * @param string $tablePrefix
     */
    public static function setTablePrefix($tablePrefix) {
        self::$tablePrefix = $tablePrefix;
        Samus_CRUD::setTablePrefix($tablePrefix);
    }

    /**
     * Obtem as linguagens do projeto
     * @return array
     */
    public static function getLangs() {
        return (array) self::$langs;
    }

    /**
     * Obtem um valor da url
     * @param $pos int
     * @return string
     */
    public static function getURL($pos = "") {
        return Samus_Keeper::getUrl($pos);
    }

    /**
     * @see Samus_Keeper::getUrlVar
     * @param string $varName
     * @return mixed
     */
    public static function getURLVar($varName = "") {
        return Samus_Keeper::getUrlVar($varName);
    }

    /**
     * Retorna a configura��o do BD
     * @return array
     */
    public static function getConnection() {
        return self::$connection;
    }

    /**
     * Especifica uma conex�o
     * @see self::$connectino
     * @param array $connection
     */
    public static function setConnection(array $connection) {
        self::$connection = $connection;
    }

    /**
     * Exibe todos os erros: error_reporting(E_ALL);
     */
    public static function errorAll() {
        error_reporting(E_ALL);
    }

    /**
     * Especifica o modo de execua��o das querys
     * @param string $queryMode
     */
    public static function setQueryMode($queryMode) {
        self::$queryMode = $queryMode;
    }

    /**
     * Obtem o modo de excu��o das querys configurado
     * @return string
     */
    public static function getQueryMode() {
        return self::$queryMode;
    }

    /**
     * Obtem o nome do controlador que esta sendo executado no momento (este m�todo
     * so pode ser excutado dentro de controladores e filtros, fora do contexto
     * n�o retornar� o nome da classe)
     *
     * Retorna o nome do controlador completamente qualificado:
     * Ex,: HomeController
     *
     * @return string nome do controladdor
     */
    public static function getControllerName() {
        return Samus_Keeper::getControllerName ();
    }

    /**
     * Obtem o host
     * @return string
     */
    public static function getHttpHost() {
        return self::$httpHost;
    }

    /**
     * Especifica o host
     * @param string $httpHost
     */
    public static function setHttpHost($httpHost) {
        self::$httpHost = $httpHost;
    }

    /**
     * Obtem a ultima url da url
     * @return string|null
     */
    public static function getLastVar() {
        if (isset($_GET [Samus::LAST_URL_VAR])) {
            if ($_GET [Samus::LAST_URL_VAR] == 'sfAsynchronous') {
                return $_GET [0];
            }
            return $_GET [Samus::LAST_URL_VAR];
        } else {
            return null;
        }
    }

}

require_once 'custom.functions.php';