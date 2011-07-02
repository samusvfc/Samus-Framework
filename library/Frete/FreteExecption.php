<?php

/**
 * Description of Frete_Execption
 *
 * @author Vinicius
 */
class Frete_Execption extends Exception {

    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }

}