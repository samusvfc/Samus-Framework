<?php

/**
 * Classe Samus_Keeper -
 * Seja um bom fazendeiro e tenha um Rebanho gordo e produtivo, o Fazendeiro vai
 * cuidar dos seus controladors de uma forma bem simples, mas ele trabalha bastante.
 * <br />
 * Ele é responsável por fazer o tratamento das URL (mod_rewrite do apache deve
 * estar habilitado pra tudo funcionar), tratando as URL ele sabe exatamente o
 * que deve executar, conforme os nome passadados ele vai incluir e mandar os
 * controladors executarem corretamente suas tarefas.
 * <br />
 * <br />
 * controladors são as classes "Samus_Controller", que são as classes de Controle <br />
 *
 * @author Vinicius Fiorio Custódio - Samusdev@gmail.com
 * @package Samus
 */
class Samus_Keeper extends Samus_Object {

    /**
     * Array com as urls do site
     * @var string[]
     */
    private static $url;
    /**
     * Um array de variaveis definidas na url Ex.:
     * pagina.com/downloads-cod=3-pag=2
     *
     * @var array
     */
    private static $urlVars = array();
    /**
     * Separador de valores na url
     * @var string
     */
    private $urlSeparator = "-";
    /**
     * Separador de variaveis na url
     * @var string
     */
    private $urlVarSeparator = "=";
    /**
     * Separador alternativo de variaveis na URL
     * @var string
     */
    private $urlAlternativeVarSeparator = ":";
    /**
     * Página default que será usada em caso de erro
     *
     * @var string
     */
    private $defaultPage = "index";
    /**
     * Página que é exibida em caso de erro
     *
     * @var string
     */
    private $errorPage = "index";
    /**
     * Filtro do pacote atual
     * @var __Filter
     */
    private $filter;
    /**
     * Nome do controlador atual
     * @var string
     */
    private static $controllerName;
    private static $slashVars;

    const DEFAULT_CONTROL_DIR = "site";
    const DEFAULT_CONTROLLER = "_";

    /**
     * Construtor da classe, indica a regra de exibição da página
     */
    function __construct($urlString) {
        $this->urlRule($urlString);
        $this->setErrorPage(WEB_URL . "index");
    }


    /**
     * Analisa a URL e obtem todos as variaveis $_GET
     * @return array
     */
    private function GETVarsRule() {

    }

    /**
     * Regra de tratamento da url
     *
     */
    public function urlRule($urlString = '') {

        if (empty($urlString)) {
            $urlString = $_GET [Samus::GET_URL_VAR];
        }

        if (Samus::DECODE_UTF8_STRINGS) {
            $urlString = utf8_decode($urlString);
        }

        self::setUrl(explode($this->getUrlSeparator(), $urlString));

        $a = explode('/', $urlString);

        if (isset($a[count($a) - 1]) && (count($a) - 1) != 0) {
            self::$url[] = $a[count($a) - 1];
            self::$urlVars[Samus::LAST_URL_VAR] = $a[count($a) - 1];
        }

        foreach (self::getUrl() as $u) {
            $uArray = explode("=", $u);
            if (count($uArray) > 1) {
                self::$urlVars [$uArray [0]] = $uArray [1];
            }
        }

        if (Samus::DECODE_UTF8_STRINGS) {
            foreach (self::$urlVars as $k => $u) {
                self::$urlVars [$k] = utf8_decode($u);
            }
        }


        $_GET = self::$urlVars;

        if (Samus::USE_CLASSIC_GET_VARS) {

            ////////////////////////////////////////////////////////////////////////
            // HABILITANDO O $_GET PADRÃO
            // através da URL atual pego as variaveis da url e adiciono ao método get
            ////////////////////////////////////////////////////////////////////////

            $str = strstr(($_SERVER['REQUEST_URI']), '?');
            
            if ($str) {
                $vars = explode('&', html_entity_decode($str));

                $auxVars = array();
                foreach ($vars as $k => $v) {

                    $a = explode('=', $v);

                    $a[0] = str_replace("[]","",$a[0]);

                    if ($k == 0) {
                        $a[0] = substr($a[0], 1);
                    }
                    if (array_key_exists($a[0], $auxVars)) {
                        if (!is_array($auxVars[$a[0]])) {
                            $auxVal = $auxVars[$a[0]];
                            $auxVars[$a[0]] = array();
                            $auxVars[$a[0]][] = $auxVal;
                        }
                        $auxVars[$a[0]][] = $a[1];
                    } else {
                        $auxVars[$a[0]] = $a[1];
                    }
                }

                foreach ($auxVars as $key => $a) {
                    $_GET[$key] = $a;
                }
            }


        }
    }

    /**
     * Inclui o arquivo correto do site
     *
     * @param string $pageName
     */
    public function displayPage() {


        $filterClass = "";
        $url = self::getUrl();
        if (empty($url [0]) || $url[0] == self::DEFAULT_CONTROL_DIR) {
            $url [0] = self::DEFAULT_CONTROL_DIR . '/' . $this->getDefaultPage();
            $url [1] = $this->getDefaultPage();
        }


        $urlDir = explode("/", $url [0]);

        $rootDirs = array();

        $d = new DirectoryIterator(WEB_DIR . Samus::PUBLIC_DIR);
        while ($d->valid()) {
            if ($d->isDir()) {
                $rootDirs[] = $d->getBasename();
            }
            $d->next();
        }


        if (!in_array($urlDir[0], $rootDirs)) {
            $aux = $urlDir;
            $urlDir = array();
            $urlDir[0] = Samus::DEFAULT_PUBLIC_DIR;
            foreach ($aux as $k => $a) {
                $urlDir[$k + 1] = $a;
            }
        } else {
            $urlDir[] = Samus::DEFAULT_PUBLIC_CONTROLLER;
        }

        if (is_dir(WEB_DIR . 'public/' . $url[0])) {
            $urlDir[] = Samus::DEFAULT_PUBLIC_CONTROLLER;
        }


        $directory = "";

        ////////////////////////////////////////////////////////////////////////
        // IMPLEMENTAÇÃO DO SLASH VARS
        ////////////////////////////////////////////////////////////////////////
        $cumulativeString = "";
        $urlControlPosition = 0;
        $dirName = "";
        $superDir = "";
        $realControllerArray = array(); //armazena os controladores que existem
        $slashMethods = array();

        foreach ($urlDir as $key => $u) {

            //$portion = Util_String::strstr($u, '.', true);

            $a = explode(Samus::METHOD_URL_SEPARATOR, $u, 2);
            $portion = $a[0];

            if ($portion) {
                $u = $portion;
            }

            // $fileName = WEB_DIR . Samus::CONTROLS_DIR . $cumulativeString . '/' . ucfirst(Util_CleanString::clean($u)) . Samus::CONTROLS_CLASS_SUFIX . Samus::CONTROLS_FILES_EXTENSION;


            $fileName = WEB_DIR . substr(Samus::PUBLIC_DIR, 0, -1) . $cumulativeString . '/'
                        . lcfirst(Util_CleanString::clean($u, true)) . '/'
                        . ucfirst(Util_CleanString::clean($u, true))
                        . Samus::CONTROLS_CLASS_SUFIX . Samus::CONTROLS_FILES_EXTENSION;

            $dirName = WEB_DIR . substr(Samus::PUBLIC_DIR, 0, -1) . $cumulativeString . '/'
                       . Util_CleanString::clean($u, true) . '/';

            $cumulativeString .= '/' . $u;

            if (is_file($fileName)) {
                $realControllerArray[] = array("fileName" => $fileName, "controlPosition" => $key);
            }

            $superDir = $dirName; // amazena o diretorio superio ao diretorio atual
        }

        if (isset($realControllerArray[count($realControllerArray) - 1]['controlPosition'])) {
            $urlControlPosition = $realControllerArray[count($realControllerArray) - 1]['controlPosition'];
        }

        if (isset($realControllerArray[count($realControllerArray) - 1])) {
            $fileName = $realControllerArray[count($realControllerArray) - 1]['fileName'];
        }


        $finalUrlDir = array();
        $slashVars = array();

        for ($i = 0; $i <= $urlControlPosition; ++$i) {
            $finalUrlDir[$i] = $urlDir[$i];
        }

        for ($i = $urlControlPosition + 1; $i < count($urlDir); ++$i) {

            if (strstr($urlDir[$i], ':')) {

                $a = explode(":", $urlDir[$i]);
                for ($ii = 0; $ii < count($a); $ii += 2) {

                    if (Samus::DECODE_UTF8_STRINGS) {
                        $slashVars[$a[$ii]] = utf8_decode($a[$ii + 1]);
                    } else {
                        $slashVars[$a[$ii]] = $a[$ii + 1];
                    }
                }
            } else {
                if (Samus::DECODE_UTF8_STRINGS) {
                    $slashVars[] = utf8_decode($urlDir[$i]);
                } else {
                    $slashVars[] = $urlDir[$i];
                }
            }
        }

        foreach ($slashVars as $k => $s) {
            $_GET[$k] = $s;
        }

        self::$slashVars = $slashVars;
        $urlDir = $finalUrlDir;


        // localizo o controlador pela URL

        if (count($urlDir) > 1) {

            $directory = "";

            $className = array_pop($urlDir);
            $className = ucfirst($className);

            // encontro os metodos da url
            $metodos = explode(Samus::METHOD_URL_SEPARATOR, $className);

            $className = $metodos [0];

            unset($metodos [0]);

            foreach ($urlDir as $dir) {
                $directory .= $dir . "/";
            }


            /*
             * CLASSE FILTRO
             * classes de filtro devem ter o mesmo nome do pacote (mas com a
             * primeira maiuscula) seguidas do sufixo definido em Samus::$filterSufix
             * e são sempre inseridos e executados quando qualquer classe do pacote
             * são inseridas
             */
            $filterClass = ucfirst($urlDir [count($urlDir) - 1]);
            $filterClass .= Samus::FILTER_SUFIX;
        } else {

            $className = ucfirst($url [0]);
            // encontro os metodos da url
            $metodos = explode(Samus::METHOD_URL_SEPARATOR, $className);

            $className = $metodos [0];

            unset($metodos [0]);
        }


        foreach ($slashVars as $m) {
            $metodos[] = $m;
        }


        Samus::$atualDir = $directory;

        /*
         * INCLUSÃO DO FILTRO
         * as classes de filtro devem ter o mesmo nome do pacote e devem imple-
         * mentar a interface Samus_Filter
         */
        $filterFile = WEB_DIR . Samus::PUBLIC_DIR . $directory . $filterClass . Samus::CONTROLS_FILES_EXTENSION;
        /*
         * CLASSE FILTRO DEFAULT
         * caso não tenha um filtro associado ele busca o filtro padrão
         */
        if (!is_file($filterFile)) {
            $filterClass = Samus::DEFAULT_FILTER_CLASS;
            $filterFile = $directory . $filterClass . Samus::CONTROLS_FILES_EXTENSION;
        }

        $classFile = $className; //nome do arquivo
        $className = Util_CleanString::clean(Util_String::underlineToUpper($className)); //nome da classe


        if (Samus::DECODE_UTF8_STRINGS) {
            $className = utf8_decode($className);
        }

        $className = Util_CleanString::clean($className, true);
        $requiredClassName = $className;
        
        Samus_Controller::setControllerName($className);
        
        
        $className .= Samus::CONTROLS_CLASS_SUFIX;

        $className = ucfirst($className);

        self::$controllerName = $className;
        

        $filtred = false;
        if (is_file($filterFile)) {

            require_once $filterFile;

            eval('$o = new ' . $filterClass . '(); ');

            $this->filter = $o;

            /* @var $met ReflectionMethod */
            eval('$exceptionsPages = $o->getExceptions();');

            $isFiltred = true;

            foreach ($exceptionsPages as $control) {
                if (strtolower($control) == strtolower($className)) {
                    $isFiltred = false;
                    break;
                }
            }

            // se a página não for uma exeção
            if ($isFiltred) {
                eval('$o->filter(); $o->endFilter();');
            }

            $filtred = true;
        }

        /**
         * CSS e JS GLOBAL
         * Encontro automaticamente os arquivos css e js do superDiretorio atual
         */
        $superDirName = $urlDir [count($urlDir) - 1];

        $cssFile = Samus::PUBLIC_DIR . $directory . Samus::CSS_DIR . $superDirName . '.css';


        if (is_file(WEB_DIR . $cssFile)) {
            $cssUrl = APP_URL . $cssFile;
            Samus_Template::addGlobalHeadRender('<link rel="stylesheet" type="text/css" href="' . $cssUrl . '" />');
        }


        $jsFile = Samus::PUBLIC_DIR . $directory . Samus::JS_DIR . $superDirName . '.js';
        if (is_file(WEB_DIR . $jsFile)) {
            $jsUrl = APP_URL . $jsFile;
            Samus_Template::addGlobalHeadRender("<script type='text/javascript' src='$jsUrl'></script>");
        }

        /*
         * RenderFile - Localizo o arquivo de renderização
         */
        $renderFile = Samus::PUBLIC_DIR . $directory . $superDirName . Samus::VIEWS_FILE_EXTENSION;

        if (!is_file(WEB_DIR . $renderFile)) {
            $renderFile = "";
        }


        //$requireFile = Samus::CONTROLS_DIR . '/' . $directory . $className . Samus::CONTROLS_FILES_EXTENSION;
        $requireFile = $fileName;
        if (!is_file($requireFile)) {
            //Busco o controlador Default
            $requireFile = $directory . self::DEFAULT_CONTROLLER . Samus::CONTROLS_CLASS_SUFIX . Samus::CONTROLS_FILES_EXTENSION;
            $className = self::DEFAULT_CONTROLLER . Samus::CONTROLS_CLASS_SUFIX;
        }

        if (is_file($requireFile)) {
            require_once $requireFile;

            $ref = new ReflectionClass($className);
            /* @var $obj Samus_Controller */
            $obj = $ref->newInstance();

            if (!empty($renderFile)) {
                $obj->setRenderFile(WEB_DIR . $renderFile);
            }

            if ($filtred) {
                $met = $ref->getMethod("setGlobal");
                $met->invoke($obj, $this->filter);
            }

            if (!empty($metodos)) {
                foreach ($metodos as $met) {

                    $metParametros = explode(Samus::METHOD_URL_PARAMETER_SEPRATOR, $met);

                    $met = $metParametros [0];
                    unset($metParametros [0]);

                    $met = Util_String::underlineToUpper($met); //nome da classe


                    if (Samus::DECODE_UTF8_STRINGS)
                        $met = utf8_decode($met);

                    $met = Util_CleanString::clean($met, true);
                    $metName = $met;
                    $tplMet = $met . Samus::METHOD_TEMPLATE_SUFIX;
                    $met = $met . Samus::METHOD_URL_SUFIX;

                    if (!method_exists($obj, $met) && !method_exists($obj, $tplMet)) {
                        if ($met == "ExitAction") {
                            return 0;
                        } else {

                        }
                    } else {
                        $isTemplate = false;
                        if (method_exists($obj, $tplMet)) {
                            $met = $tplMet;
                            $isTemplate = true;
                        }


                        $urlMet = $ref->getMethod($met);

                        if (!empty($metParametros)) {

                            try {
                                $urlMet->invokeArgs($obj, $metParametros);
                            } catch (ReflectionException $ex) {
                                throw new Samus_Exception("Você não tem permissão para acessar este metodo ou ele é invalido " . $ex->getMessage());
                            }
                        } else {

                            try {
                                $urlMet->invoke($obj);
                            } catch (ReflectionException $ex) {
                                throw new Samus_Exception("Você não tem permissão para acessar este metodo ou ele é invalido " . $ex->getMessage());
                            }
                        }

                        if ($isTemplate) {
                            $methodTemplate = $directory . $requiredClassName . '/' . $metName;
                            /* @var $obj Samus_Controller */
                            $obj->addAuxTemplate($methodTemplate . Samus::VIEWS_FILE_EXTENSION);
                        }
                    }
                }
            }
            /* @var $met ReflectionMethod */
            eval('$obj->index();');
            eval('$obj->assignClass("' . Samus::PUBLIC_DIR . $directory . '");');
        } else {

            /*             * *************************************************************
             * EXIBIÇÃO DE ARQUIVOS SEM CONTROLADORES ASSOCIADOS
             * ************************************************************ */
            //$className = strtolower(substr($className, 0, 1)) . substr($className,1);
            //caso seja um arquivo de template


            if (empty($className)) {
                $className = $this->getDefaultPage();
            }
            if (substr($className, -8, 8) == '.inc.php') {

                //$requireViewFile = Samus::VIEWS_DIR . '/' . strtolower($className);
                $requireViewFile = $dirName . strtolower($className);
                require $requireViewFile;
            } else {

                //$requireViewFile = Samus::VIEWS_DIR . '/' . $directory . Util_String::upperToUnderline($classFile) . Samus::VIEWS_FILE_EXTENSION;
                $requireViewFile = $dirName . Util_String::upperToUnderline($classFile) . Samus::VIEWS_FILE_EXTENSION;
                ;

                if (is_file($requireViewFile)) {
                    require_once 'Samus/Samus_DefaultController' . Samus::CONTROLS_FILES_EXTENSION;

                    $ref = new ReflectionClass("Samus_DefaultController");
                    $obj = $ref->newInstance();

                    /* @var $met ReflectionMethod */
                    $met = $ref->getMethod("index");
                    $met->invoke($obj);

                    if ($filtred) {
                        $met = $ref->getMethod("setGlobal");
                        $met->invoke($obj, $o);
                    }

                    $met = $ref->getMethod("assignClass");
                    $met->invoke($obj, $requireViewFile);
                } else {

                    require_once 'util/Util.php';
                    //echo "<h1 align='center'>Página não Encontrada!</h1>";
                    //echo "<h2 align='center'>".$_SERVER['REQUEST_URI']."</h2>";


                    $strA = '';
                    foreach (Samus::getURL() as $st) {
                        $strA .= $st . '-';
                    }

                    $strA = substr($strA, 0, -1);

                    if (substr($strA, -5) != "index") {

                        echo "Requisição não processada";
                    } else {

                        //Util::redirect($this->errorPage.'-'.Samus::getURL(0), 0);
                    }
                }
            }
        }


        if (isset($endFilter) && isset($o)) {
            $endFilter->invoke($o);
        }
    }

    /**
     * Retorna um valor da url, todas as variáveis $_GET devem ser obtidas por
     * este método, cada valor do array fica em uma posição da URL:
     * Ex.:  <br />
     * http://site.com.br/produto-categoria-2<br />
     * <br />
     * Samus_Keeper::getUrl(0); // retorna "produto"<br />
     * Samus_Keeper::getUrl(1); // retorna "categoria"<br />
     * Samus_Keeper::getUrl(2); // retorna 2<br />
     * Samus_Keeper::getUrl();  // retorna array("produto", "categoria" , 2); <br />
     *
     * @return string[]
     */
    public static function getUrl($pos = "") {
        if (!empty($pos) || $pos === 0) {

            if (!empty(self::$url [$pos])) {
                return self::$url [$pos];
            } else {
                return NULL;
            }
        } else {

            return self::$url;
        }
    }

    /**
     * Obtem uma variavel de url
     *
     * Ex.:
     * pagina.com/download-cod=13-ref=14-user-2001
     *
     * Samus::getUrlVar('cod');
     * Samus_Keeper::getUrlVar('ref');
     *
     * @param <type> $varName
     * @return <type>
     */
    public static function getUrlVar($varName = null) {
        if (!empty($varName) || $varName === 0) {

            if (!empty(self::$urlVars [$varName])) {
                return self::$urlVars [$varName];
            } else {
                return NULL;
            }
        } else {

            return self::$urlVars;
        }
    }

    /**
     * @param string[] $url
     */
    public static function setUrl($url) {
        self::$url = $url;
    }

    /**
     * @return string
     */
    public function getUrlSeparator() {
        return $this->urlSeparator;
    }

    /**
     * @param string $urlSeparator
     */
    public function setUrlSeparator($urlSeparator) {
        $this->urlSeparator = $urlSeparator;
    }

    /**
     * @return string
     */
    public function getDefaultPage() {
        return $this->defaultPage;
    }

    /**
     * @param string $defaultPage
     */
    public function setDefaultPage($defaultPage) {
        $this->defaultPage = $defaultPage;
    }

    /**
     * @return string
     */
    public function getErrorPage() {
        return $this->errorPage;
    }

    /**
     * @param string $errorPage
     */
    public function setErrorPage($errorPage) {
        $this->errorPage = $errorPage;
    }

    /**
     * Obtem o filtro do pacote atual
     * @return __Filter
     */
    public function getFilter() {
        return $this->filter;
    }

    /**
     * Obtem o nome do controlador que esta sendo executado na requisição
     * @return string
     */
    public function getControllerName() {
        return self::$controllerName;
    }

}