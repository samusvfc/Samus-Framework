<?php
/**
 * Esta classe é invocada quando uma views sem controlador é invocadas
 *
 * @package Samus
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 *
 */
class Samus_DefaultController extends Samus_Controller {

    public function index() {
        $this->setTemplateFile(false);
    }


}