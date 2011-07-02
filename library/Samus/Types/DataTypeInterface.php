<?php

interface DataTypeInterface {

    /**
     * Obtem o tipo de dado 
     * @return string
     */
    public function getType();

    public static function getInstance($arg=null);

    public static function cast($object);
    
    public function __construct();
    
}

?>