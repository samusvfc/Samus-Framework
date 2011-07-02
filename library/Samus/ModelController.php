<?php

/**
 * Samus_CO
 * Esta classe representa um Controlador de Modelos, todo método com funções especificas
 * de uma classe deve extender à esta classe.
 *
 *
 * @author Vinicius Fiorio Custódio - samusdev@gmail.com
 * @package Samus
 */
abstract class Samus_ModelController extends Samus_Object {

    /**
     * @var Samus_Model Instancia de um modelo Samus_Model
     */
    protected $object;

    /**
     * Sufixo que deve ser utilizado para o nome dos Controladores de Modelos.
     * 
     * Ex.:
     * class Pessoa extends Samus_Model {}
     *
     * class PessoaCO etends Samus_CO {}
     *
     * @var string
     * @static
     * @final
     */
    const DEFAULT_CO_SUFIX = "CO";

    public function  __construct(Samus_Model $object) {
        $this->object = $object;
    }

    public function getObject() {
        return $this->object;
    }

    public function setObject($object) {
        $this->object = $object;
    }

    /**
     * Obtem uma instance de DAO do Objeto atual
     * @return DAO_CRUD
     */
    public function getDao() {
        return $this->object->getDao();
    }


}