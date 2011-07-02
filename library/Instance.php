<?php

class Instance extends Samus_Object {

    public static function off($className) {
        $className = str_replace(".", "_", $className);
        return new $className;
    }

}