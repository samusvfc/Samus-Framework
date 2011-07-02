<?php

header('Content-type: text/css');

abstract class Samus_CSS extends Samus_Controller {

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
        parent::__construct(Samus_Controller::MODE_CSS);

        $this->dir = WEB_DIR;
        $this->url = WEB_URL;
    }

}