<?php

/**
 * Description of Samus_RootController
 *
 * @author Vinicius
 */
abstract class Samus_RootController extends Samus_Controller {

    public function  __construct($mode = "view") {
        parent::__construct($mode);
        $this->setTemplateFile(false);
    }


}