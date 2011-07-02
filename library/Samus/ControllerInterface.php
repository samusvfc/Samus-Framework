<?php

/**
 * Interface que especifнca todos os mйtodos que os controladores devem implementar
 * @author Vinicius Fiorio Custуdio
 * @package Samus
 */
interface Samus_ControllerInterface {

    /**
     * Mйtodo que й chamado para iniciar qualquer controle
     *
     */
    public function index();

    /**
     * Este mйtodo й responsбvel pela ponte entre o Controlador e o Template
     * associado, й sempre executado depois da chamada ao mйtodo index
     *
     * @param string $directory
     * @param string $metodo
     * @param array $args
     */
    public function assignClass($directory="", $metodo="", array $args = array());


    /**
     * Mйtodo invocado apуs a renderizaзгo da pбgina
     */
    public function close();

}