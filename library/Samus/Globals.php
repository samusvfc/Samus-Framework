<?php

/**
 * Description of Samus_Globals
 *
 * @author Vinicius
 */
class Samus_Globals extends Samus_Object {

    /**
     * Obtem o valor de uma variavel global qualquer da variavel $_GET
     *
     * @param mixed $index
     */
    public static function get($index) {
        if (isset($_GET[$index])) {
            return $_GET[$index];
        } else {
            return null;
        }
    }

    /**
     * Obtem o valor de uma variavel global qualquer da variavel $_POST
     *
     * @param mixed $index
     */
    public static function getPost($index) {
        if (isset($_POST[$index])) {
            return $_POST[$index];
        } else {
            return null;
        }
    }

    /**
     * Obtem o valor de uma variavel global qualquer da variavel $_SERVER
     *
     * @param mixed $index
     */
    public static function getServer($index) {
        if (isset($_SERVER[$index])) {
            return $_SERVER[$index];
        } else {
            return null;
        }
    }

}