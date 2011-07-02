<?php
require_once 'Samus/View/smarty3/libs/Smarty.class.php';
/**
 * Classes de Controle - Samus_Controller(controlador)
 * <br />
 * As modelos(classes Samus_Model) são nossos modelos queridos, mas são os controladors(classes Samus_Controller)
 * que botam pra fazer, nossos controladors também são ótimos reprodutores, gerando
 * instancias das nossas modelos para todo mundo ver. Mas nossos controladors são educados
 * e cuprem corretamente o que o Fazendeiro(Samus_Keeper) mandam ele fazer.
 * <br />
 * <br />
 * As classes Samus_Controller funcionam assim: qualquer propriedade da classe que estiver
 * ecapsulada (com seu getter e setter) poderá ser utilizado no contexo da
 * visão caso ela tenhao seu getter especificado (private $nome | getNome()),
 * caso o atributo seja publico ele também poderá ser utilizado no contexto da
 * visão. O arquivo de visão deve estar no diretório das views e deve ter o
 * mesmo nome da classe Samus_Controller, e importante a classe Samus_Controller deve implementar o método
 * index() (que faz parte da interface SamusController) este método é executado
 * sempre que a visão associada é chamada: <br />
 * Ex.:<br />
 * Controlador: <br  />
 * classes/controls/Conteudo.php <br />
 * class Conteudo extends Samus_Controller {<br />
 * 	   private $nome;<br />
 * 	   public  $valor = "um valor qualquer";
 * <br />
 *     public function index() {<br />
 * 		   $this->setNome("Vinicius Fiorio Custódio");<br />
 *     }<br />
 * <br />
 *   public function getNome() {<br />
 * 	    return $this->nome;<br />
 * 	 }<br />
 * <br />
 *   public function setNome($nome) {<br />
 *      $this->nome = $nome;<br />
 *   }<br />
 * <br />
 * }<br />
 *
 * <br />
 * <br />
 * Visão: <br />
 * views/conteudo.tpl <br />
 * ...${ $nome } is ${ $valor }..<br />
 * <br />
 *
 * @author Vinicius Fiorio Custódio
 * @package Samus
 */
abstract class Samus_Controller extends Samus_Object implements Samus_ControllerInterface {

    /**
     * Caso exista, o filtro do pacote
     *
     * @var mixed
     */
    protected $global;
    /**
     * Nome do template que será exibido
     *
     * @var string nome do arquivo template
     */
    private $templateFile = "";
    /**
     * Objeto smarty da página
     *
     * @var Smarty
     */
    protected $smarty;
    private $_decodeVars = true;
    private $mode; // define para qual tipo de arquivo este controlador é destinado
    /**
     * Nome do arquivo que vai renderizar a pagina corrente
     * @var string
     */
    private $renderFile;


    const MODE_VIEW = "view";
    const MODE_CSS = "css";
    const MODE_JAVASCRIPT = "js";

    public $webDir;
    public $webURL;
    public $dir;
    /**
     * Samus Template Funcions
     * @var Samus_Template
     */
    public $s;
    private $cssFile;
    private $jsFile;
    private $headFile;
    private $_title;
    private $smartRender = false;
    /**
     * Templates auxiliares
     * @var array
     */
    private $_auxTemplates = array();
    private $_sfAsynchronous = false;
    private $enableCache = false;

    private static $controllerName;
    
    public function __construct($mode="view") {
        //parent::Smarty();
        $this->webDir = WEB_DIR;
        $this->webURL = WEB_URL;

        $this->mode = $mode;

        /* @var $smarty Smarty */
        $this->smarty = Samus_CRUD_Singleton::getInstance("Smarty");

        if ($this->enableCache) {
            $this->enableCache();
        }

        $this->smarty->error_reporting = E_ERROR;
        $this->smarty->left_delimiter = Samus::VIEWS_LEFT_DELIMITER;
        $this->smarty->right_delimiter = Samus::VIEWS_RIGHT_DELIMITER;
        $this->smarty->compile_dir = Samus::COMPILED_VIEWS_DIR;
        $this->smarty->template_dir = '../'.Samus::PUBLIC_DIR;
        //$this->smarty->php_handling = SMARTY_PHP_ALLOW;
        $this->smarty->cache_dir = APP_URL.'system/cache';

        $this->smarty->config_dir = APP_URL . "system/configs";


        $this->s = new Samus_Template($this);



    }

    public function enableCache() {
        $this->smarty->cache_id = $_SERVER['REQUEST_URI'];
        $this->smarty->caching = true;
        $this->smarty->cache_lifetime = 3600;
    }

    /**
     * Adiciona um template auxiliar
     * @param $auxTemplate
     */
    public function addAuxTemplate($auxTemplate) {
        $this->_auxTemplates[] = $auxTemplate;
    }

    public function get_auxTemplates() {
        return $this->_auxTemplates;
    }

    /**
     * Define o titulo da página atual
     * @param  $title
     */
    public function setTitle($title) {
        $this->s->setTitle($title);
    }

    /**
     * Método responsável pela exibição da página, por aqui é feita a mágica de
     * colocar todas as propriedades encapsuladas na visão
     *
     * @param string $directory diretorio do template
     * @param string $metodo método de filtro
     * @param array $args argumentos do método de filtro
     */
    public function assignClass($directory="", $metodo="", array $args = array()) {

        $this->dir = APP_URL . Samus::PUBLIC_DIR . Samus::$atualDir;

        $this->s->setImgDir($this->dir.Samus::CSS_DIR."images/");
        $this->s->setJsDir($this->dir.Samus::JS_DIR."images/");



        $this->assignGlobals();

        $directory = WEB_DIR . $directory;
        
        $ref = new ReflectionClass($this);

        if (!empty($metodo)) {
            /* @var $refMetodo ReflectionMethod */
            $refMetodo = $ref->getMethod($metodo);
            $refMetodo->invoke($this, $args);
        }

        $ref = new ReflectionObject($this);
        $met = $ref->getMethods();
        $ai = new ArrayIterator($met);
        $minhaArray = array();
        $this->smarty->assign("this", $this);

        while ($ai->valid()) {

            $metName = $ai->current()->getName();

            if (substr($metName, 0, 3) == "get" && $metName{3} != "_") {
                $prop = substr($metName, 3);
                $prop = strtolower(substr($prop, 0, 1)) . substr($prop, 1);

                $minhaArray[$prop] = $ai->current()->invoke($this);

                /**
                 * Atribui para a classe de visão todas as propriedades do
                 * objeto depois de executar o método especificado (aki é a
                 * chave do negócio)
                 */
                $this->smarty->assign($prop, $ai->current()->invoke($this));
            }

            $ai->next();
        }

        $properties = $ref->getProperties();

        foreach ($properties as $prop) {
            /* @var $prop ReflectionProperty */
            if ($prop->isPublic()) {
                $this->smarty->assign($prop->getName(), $prop->getValue($this));
            }
        }

        // $this->assignGlobals(); // o carregamento é agora condicional, à chamada "getGlobal()"


        $this->smarty->assign("s", $this->s);

        if (empty($this->templateFile) && $this->templateFile !== false) {

            /**
             * Exibe um template com o mesmo nome da classe especificada com a primeira
             * letra como minuscula
             */
            $templateName = substr($ref->getName(), 0, strlen(Samus::CONTROLS_CLASS_SUFIX) * -1);


            if ($this->mode == self::MODE_VIEW) {

                //$cssFile = WEB_DIR . Samus::CSS_DIR . $directory . $templateName . '.css';



                $cssFile = $this->directory . lcfirst($templateName) . '/' . $templateName . '.css';

                /**
                 * @todo verificar se esta linha esta errada ta parecendo que na versão
                 * online não esta inserindo este arkivo
                 */
                if (file_exists(WEB_DIR . Samus::PUBLIC_DIR . Samus::$atualDir . $cssFile)) {
                    $this->cssFile = $this->dir . $cssFile;
                }

                $jsFile = $this->directory . lcfirst($templateName) . '/' . $templateName . '.js';

                if (is_file(WEB_DIR . Samus::PUBLIC_DIR . Samus::$atualDir . $jsFile)) {
                    $this->jsFile = $this->dir . $jsFile;
                }


                $headFile = $this->directory . lcfirst($templateName) . '/' . $templateName . '_Head' . Samus::VIEWS_FILE_EXTENSION;
                if (file_exists(WEB_DIR . Samus::PUBLIC_DIR . Samus::$atualDir . $headFile)) {
                    $this->headFile = Samus::$atualDir . $headFile;
                }


                $realTemplateFile = $directory . lcfirst($templateName) . '/' . $templateName . Samus::VIEWS_FILE_EXTENSION;
                $this->templateFile = $realTemplateFile;
            } elseif ($this->mode == self::MODE_JAVASCRIPT) {

                $realTemplateFile = WEB_DIR . $directory . $templateName . Samus::JS_FILE_EXTENSION;
            } elseif ($this->mode == self::MODE_CSS) {
                $realTemplateFile = WEB_DIR . Samus::CSS_DIR . $directory . $templateName . Samus::CSS_FILE_EXTENSION;
            } else {

                $realTemplateFile = $directory . $templateName . Samus::VIEWS_FILE_EXTENSION;
            }

            //$this->smarty->display( $realTemplateFile);

            $renderFile = $this->getRenderFile();
            if (!empty($renderFile)) { //se um arquivo de renderização estiver setado
                $this->templateFile = $realTemplateFile;
                $this->smarty->display($this->getRenderFile());
            } else {

                $this->smarty->display($this->templateFile);
            }
        } elseif ($this->templateFile === false) {
            $cssFile = WEB_DIR . Samus::CSS_DIR . $this->templateFile . '.css';
            $cssUrl = $this->dir . Samus::CSS_DIR . $this->templateFile . '.css';
            $jsFile = WEB_DIR . Samus::JS_DIR . $this->templateFile . '.js';

            if (is_file($jsFile)) {
                $this->smarty->assign("sf_current_js", WEB_URL . Samus::JS_DIR . $this->templateFile . '.js');
            }
            $this->smarty->display("sf/Empty.html");
        } else {
            $renderFile = $this->getRenderFile();
            if (!empty($renderFile)) { //se um arquivo de renderização estiver setado
                $this->smarty->display($this->getRenderFile());
            } else {
                $this->smarty->display($this->templateFile);
            }
        }


        $this->close();
    }

    /**
     * Metodo generico executado ao fim do controlador
     */
    public function close() {
        
    }

    /**
     * Envia para o template tudo que for globalF
     */
    private function assignGlobals() {

        $constantes = get_defined_constants(true);

        if ($this->_decodeVars) {

            $_GET = Util_String::utf8ArrayDecode($_GET);
        }

        $varsArray = array(
            "post" => $_POST,
            "get" => $_GET,
            "const" => $constantes['user'],
            "url" => Samus_Keeper::getURL(),
            "urlVar" => Samus_Keeper::getURLVar()
        );

        if (!empty($_SESSION)) {
            $varsArray[] = $_SESSION;
        }

        
        $this->smarty->assign("samus", $varsArray);
    }

    /**
     * @return string
     */
    public function getTemplateFile() {
        return $this->templateFile;
    }

    /**
     * Especifica o arquivo que servira como visão
     * @param string $templateFile
     */
    public function setTemplateFile($templateFile) {
        if (substr($templateFile, count(Samus::VIEWS_FILE_EXTENSION) * -1, count(Samus::VIEWS_FILE_EXTENSION) != Samus::VIEWS_FILE_EXTENSION)) {
            $templateFile .= Samus::VIEWS_FILE_EXTENSION;
        }

        $this->templateFile = $templateFile;
    }

    /**
     * @return mixed
     */
    public function getGlobal() {
        return $this->global;
    }

    /**
     * @param mixed $global
     */
    public function setGlobal($global) {
        $this->global = $global;
    }

    /**
     * Apelido para getGlobal() obtem a instancia do filtro do pacote
     * @return __Filter
     */
    public function getFilter() {
        return $this->getGlobal();
    }

    /**
     * @return Smarty
     */
    public function getSmarty() {
        return $this->smarty;
    }

    /**
     * @param Smarty $smarty
     */
    public function setSmarty($smarty) {
        $this->smarty = $smarty;
    }

    public function exitAction() {
        exit();
    }

    public function getRenderFile() {
        return $this->renderFile;
    }

    public function setRenderFile($renderFile) {
        $this->renderFile = $renderFile;
    }

    public function enableSmartRender() {
        $this->smartRender = true;
    }

    public function getCssFile() {
        return $this->cssFile;
    }

    public function getJsFile() {
        return $this->jsFile;
    }

    public function getHeadFile() {
        return $this->headFile;
    }

    public function sfAsynchronousAction() {
        $this->_sfAsynchronous = true;
        $this->setRenderFile('sf/asynchronous.html');
    }

    /**
     * Define se é uma requisição assincrona
     * @return boolean
     */
    public function isAsync() {
        return $this->_sfAsynchronous;
    }


    public function plugin($pluginName) {

    }
 
    /**
     * Especifica o nome do contorlador atual
     * @param string $controllername 
     */
    public static function setControllerName($controllername) {
        self::$controllerName = $controllername;
    }
    
    /**
     * Obtem o nome do controllador atual (nome limpo)
     * @return string
     */
    public function getControllerName() {
       return self::$controllerName;
    }

}