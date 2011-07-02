<?php
header('Content-type: application/javascript');

abstract class Samus_JavaScript extends Samus_Controller {
    
    /**
     * Contem o Diretório do projeto
     * @var string
     */
    public $dir;

    /**
     * Contém a URL do projeto
     * @var string
     */
    public $url;
    
    public function __construct() {
        parent::__construct(Samus_Controller::MODE_JAVASCRIPT);
        
        $this->dir = WEB_DIR;
        $this->url = WEB_URL;    
    }
    
}