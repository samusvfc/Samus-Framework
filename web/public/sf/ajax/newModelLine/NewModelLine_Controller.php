<?php

/**
 * Classe NewModelLine_Controller
 *
 * @author Vinicius
 */
class NewModelLine_Controller extends Samus_Controller {

    public $lineNum;

    public function index() {

        $lineNum = Samus::getLastVar();
        if(!empty($lineNum)) {
            $this->lineNum = $lineNum + 1;
        } else {
            $this->lineNum = 1;
        }

        $this->setRenderFile('');
    }

}