<?php
/**
 * Esta classe � invocada quando uma views sem controlador � invocadas
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