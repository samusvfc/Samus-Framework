<?php

/**
 * Todas as classes de filtro devem extender a esta classe, as classes Samus_Filter 
 * tambem definem valores glovais para as Views, qualquer atributo Encapsulado
 * ou p�blico declarados no Samus_Filter podem ser enchegardos por todos as Views do 
 * pacote.
 * Para acessar os valores globais basta chamar na vis�o o atributo global:
 * <| $global->atributo |>
 * 
 * As p�ginas especificadas em Exeption n�o passar�o pelo filtro
 *
 */
abstract class Samus_Filter extends Samus_Object implements Samus_FilterInterface {

    /**
     * Array com o nome das classes que s�o exe��es na execu��o do Samus_Filter
     *
     * @var array
     */
    private $exceptions = array();


    /**
     * Adiciona um controlador que ser� exe��o na hora de executar
     *
     * @param string $controllerName
     */
    public function addExeptionControl($controllerName) {
        $this->exceptions[] = $controllerName;
    }

    /**
     * @return array
     */
    public function getExceptions() {
        return $this->exceptions;
    }

    /**
     * @param array $exceptions
     */
    public function setExceptions($exceptions) {
        $this->exceptions = $exceptions;
    }

}