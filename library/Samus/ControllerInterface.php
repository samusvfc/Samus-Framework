<?php

/**
 * Interface que especif�ca todos os m�todos que os controladores devem implementar
 * @author Vinicius Fiorio Cust�dio
 * @package Samus
 */
interface Samus_ControllerInterface {

    /**
     * M�todo que � chamado para iniciar qualquer controle
     *
     */
    public function index();

    /**
     * Este m�todo � respons�vel pela ponte entre o Controlador e o Template
     * associado, � sempre executado depois da chamada ao m�todo index
     *
     * @param string $directory
     * @param string $metodo
     * @param array $args
     */
    public function assignClass($directory="", $metodo="", array $args = array());


    /**
     * M�todo invocado ap�s a renderiza��o da p�gina
     */
    public function close();

}