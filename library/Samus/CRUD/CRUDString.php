<?php

/**
 * Classe Samus_CRUD_CRUDString
 *
 * @author Vinicius
 */
class Samus_CRUD_CRUDString {

    protected $str;

    public function getStr() {
        return $this->str;
    }

    public function setStr($str) {
        $this->str = $str;
    }

    public function  __toString() {
        return $this->getStr();
    }

}