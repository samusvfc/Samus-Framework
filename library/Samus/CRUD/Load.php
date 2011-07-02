<?php
/**
 * Classe Loader
 *
 * @author Vinicius
 */
class Samus_CRUD_Load {


    /**
     * @param string $className
     * @return DAO_CRUD
     */
    public static function model($className) {
        $class = new $className;
        /* @var $class Samus_Model */
        return $class->getDao();

    }


}