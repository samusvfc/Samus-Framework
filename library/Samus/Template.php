<?php
/**
 * Description of Samus_Template
 *
 * @author Vinicius
 */
class Samus_Template extends Samus_Object {

    /**
     * @var Samus_Controller
     */
    private $controller;
    private $title;
    private $scriptsArray = array();
    private $cssArray = array();

    /*
     * Parametro statico que adiciona itens para serem renderizados no Head do documento
     * @var string
     * @static
     */
    private static $globalHeadRender;

    /**
     * Parametro static à ser renderizado no body
     * @var string
     * @static
     */
    private static $globalBodyRender;

    /**
     * Parametro statico que adiciona strings que serão renderizadas no rodapé
     * @var array
     * @static
     */
    private static $globalFooterRender;

    /**
     * Caminho para a pasta padrao de imagens
     * @var string
     */
    private $imgDir;

    /**
     * Caminho para pasta padrao de arquivos js
     * @var string
     */
    private $jsDir;
    
    public function __construct($controller) {
        $this->controller = $controller;
    }


    public static function addGlobalHeadRender($string) {
        if (!is_array(self::$globalHeadRender)) {
            self::$globalHeadRender = array();
        }
        self::$globalHeadRender[] = $string;
    }

    /**
     * Adiciona uma string para ser renderizada no body
     * @param string $string
     */
    public static function addGlobalBodyRender($string) {
        if (!is_array(self::$globalBodyRender)) {
            self::$globalBodyRender;
        }
        self::$globalBodyRender[] = $string;
    }

    /**
     * Adiciona um string para ser renderizada no rodape dos templates
     * @param string $string
     */
    public static function addGlobalFooterRender($string) {
        if (!is_array(self::$globalFooterRender)) {
            self::$globalFooterRender = array();
        }
        self::$globalFooterRender[] = $string;
    }

    public function getTitle() {
        return $this->title;
    }

    /**
     * Seta o titulo da página
     * @param $title
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     * Obtem o nome do arquivo de template que será incluido para renderização da pagina
     */
    public function getRenderFile() {
        return $this->controller->getTemplateFile();
    }

    public function getRenderFilesArray() {
        $a = array();
        $a[] = $this->getRenderFile();      
        foreach ($this->controller->get_auxTemplates() as $t) {            
            $a[] = $t;
        }
        $a = array_reverse($a);
        return $a;
    }

    public function getHeadFile() {
        return $this->controller->getHeadFile();
    }

    /**
     * Inclui um arquivo qualquer dentro do template
     * @param string $filename Nome do arquivo
     * @param boolean $once Se o arquivo sera inserido uma unica vez
     * @param array $params parametros
     */
    public function includeFile($filename, $once=false, $params=array()) {
        $this->controller->getSmarty()->_include($filename, $once, $params);
    }

    /**
     * Adiciona scripts para serem renderizados no head
     * @param script $file
     */
    public function addScript($fileUrl) {
        $this->scriptsArray[] = $fileUrl;
    }

    /**
     * Adiciona arquivos css para serem renderizados no head
     * @param $fileUrl
     */
    public function addCss($fileUrl) {
        $this->cssArray[] = $fileUrl;
    }

    /**
     * Adiciona textos para serem renderizados no Head
     * @param string $string
     */
    public function head($string) {
        self::addGlobalHeadRender($string);
    }

    /**
     * Carrega de forma assincrona uma URL qualquer
     * @param string $url
     * @param string $resultElementId
     */
    public function ajaxLoad($url, $resultElementId) {

        $str = "
<script type='text/javascript'>
$(document).ready(function() {
       var sf = new SF();
       sf.ajaxLoad($url, $resultElementId, $a);
});
</script>";

        return $str;
    }

    /**
     * Metodo obrigatorio que deve ser chamado dentro do Head dos templates de renderizacao
     * @return string
     */
    public function renderHead() {
        $parans = array(
            "sf_ajax_js" => 'scripts/samus/sf.ajax.js',
            "jquery_file" => 'scripts/jquery/jquery-1.4.2.min.js'
        );


        $str = "<base href='". APP_URL . Samus::$atualDir  ."' />
        <script type='text/javascript' src='" . WEB_URL . $parans['jquery_file'] . "'></script>" 
        ."<script type='text/javascript' src='" . WEB_URL . $parans['sf_ajax_js'] . "'></script>";


        $cssFile = $this->controller->getCssFile();
        $jsFile = $this->controller->getJsFile();

        if (!empty($this->cssArray)) {
            foreach ($this->cssArray as $css) {
                $str .= '
    <link rel="stylesheet" type="text/css" href="'.$css.'" />
                ';
            }
        }

        if (!empty($this->scriptsArray)) {
            foreach ($this->scriptsArray as $js) {
            	
                $str .= "
<script type='text/javascript' src='" . WEB_URL . $js . "'></script>;
";
            }
        }


        if (!empty($cssFile)) {
            $str .= '
        <link rel="stylesheet" type="text/css" href="'.$cssFile.'" />
            ';
        }

        if (!empty($jsFile)) {
            $str .= "
<script type='text/javascript' src='" . $jsFile . "'></script>";
        }

        if (!empty(self::$globalHeadRender)) {
            foreach (self::$globalHeadRender as $g) {
                $str .= $g;
            }
        }



        return $str;
    }

    /**
     * Metodo de renderização do Body dos templates de renderização
     * @return string
     */
    public function renderBody() {
        $str = "<div id='_sf-root'></div>";

        if (!empty(self::$globalBodyRender)) {
            foreach (self::$globalBodyRender as $r) {
                $str .= $r;
            }
        }

        return $str;
    }

    /**
     * Método de renderização dos rodapés
     * @return string
     */
    public function renderFooter() {
        $str = "";

        if (!empty(self::$globalFooterRender)) {
            foreach (self::$globalFooterRender as $f) {
                $str .= $f;
            }
        }

        return $str;
    }

    /**
     * Obtem uma data em formato brasileiro
     * @param int $estilo
     * @return string
     */
    public function dataBrasil($estilo=6) {
        $data = new Util_Datas_DataBrasil();
        return $data->getData($estilo);
    }

    /**
     * @param string $imgDir
     * @return void
     */
    public function setImgDir($imgDir) {
        $this->imgDir = $imgDir;
    }

    /**
     * @return string
     */
    public function getImgDir() {
        return $this->imgDir;
    }

    /**
     * @param string $jsDir
     * @return void
     */
    public function setJsDir($jsDir) {
        $this->jsDir = $jsDir;
    }

    /**
     * @return string
     */
    public function getJsDir() {
        return $this->jsDir;
    }


}