<?php

/**
 * Interface com as assinaturas dos m�todos de valida��o de uma classe
 * 
 * @author Vinicisu Fiorio - samusdev@gmail.com
 * @package samus
 */
interface Samus_ValidatorInterface {
    
    /**
     * Mensagem em caso de erro
     * @param string $msg
     * @return string
     */
    public function showErrorMsg($msg);
    
    /**
     * Mensagem em caso de sucesso
     * 
     * @param string $msg
     * @return string
     */
    public function showSucessMsg($msg);
    
    /**
     * Validacao para os m�todos que salvam
     * 
     * $object->getDao()->save();
     * $object->getDao()->saveObjectArray();
     * $object->getDao()->saveXml();
     * 
     * @param object $object
     * @return boolean
     */
    public function saveAction($object);
    
    /**
     * Valida��o para m�todos que deletam
     * 
     * $object->getDao()->delete();
     * $object->getDao()->ObjectArray();
     * 
     * @param object $object
     * @return boolean
     */
    public function deleteAction($object);
    
    /**
     * Valida��o para m�todos de carregamento de um objeto unicos
     * 
     * $object->getDao()->load();
     * * $object->getDao()->loadLast();
     * * $object->getDao()->loadFirst();
     * 
     * @param object $object
     * @return boolean
     */
    public function loadAction($object);
    
    /**
     * Valida��o para m�todos que carregam arrays e arrays de objetos
     * 
     * $object->getDao()->loadArrayList();
     * $object->getDao()->loadAssociativeArrayList();
     * $object->getDao()->loadLightArray();
     * 
     * @param object $object
     * @return boolean
     */
    public function loadArrayAction($object);
    
    /**
     * Executado sempre que o __tostring � invocado
     * 
     * $object->getDao()->__tostring();
     * 
     * @param object $object
     * @return boolean
     */    
    public function toStringAction($object);

    
}