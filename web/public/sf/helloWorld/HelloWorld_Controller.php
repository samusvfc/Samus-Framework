<?php

/**
 * Class HelloWorld_Controller
 *
 * @author samus
 */
class HelloWorld_Controller extends Samus_Controller {

    public $msg;

    public function index() {
        $this->msg = "Hello World!";
    }

}