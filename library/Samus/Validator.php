<?php
/**
 * Classe abstrata que implementa as validações necessárias para os métodos 
 * padrãos do CRUD
 * 
 * @author Vinicius Fiorio - samusdev@gmail.com
 * @package samus
 */
abstract class Validator extends Samus_Object implements Samus_ValidatorInterface {
	
    /**
     * @var string mensagem de erro padrão
     */
    private $errorMsg = "Ocorreu um erro, por favor verifique os dados";

    /**
     * @var string mensagem de sucesso padrão
     */
    private $sucessMsg = "";
	
    /**
     * Classe css padrão para as mensagens de erro
     * @var string
     */
    private $errorCssClass = "crud-error";
	
    /**
     * @see Samus_ValidatorInterface::showErrorMsg()
     *
     * @param string $msg
     * @return string
     */
    public function showErrorMsg($msg) {
        if (empty( $msg )) {
            $msg = $this->errorMsg;
        }
		
        return "
        	<div class='$this->errorCssClass'>
        $msg
        	 </div>
        ";
    }
	
    /**
     * @see Samus_ValidatorInterface::showSucessMsg()
     *
     * @param string $msg
     * @return string
     */
    public function showSucessMsg($msg) {
        if (empty( $msg )) {
            $msg = $this->sucessMsg;
        }
		
        return "$msg";
    }
	
    /**
     * @see Samus_ValidatorInterface::constructorAction()
     *
     * @return boolean
     */
    public function constructorAction() {
        return true;
    }
	
    /**
     * @see Samus_ValidatorInterface::deleteAction()
     *
     * @return boolean
     */
    public function deleteAction($object) {
        return true;
    }
	
    /**
     * @see Samus_ValidatorInterface::loadAction()
     *
     * @return boolean
     */
    public function loadAction($object) {
        return true;
    }
	
    /**
     * @see Samus_ValidatorInterface::loadArrayAction()
     *
     * @return boolean
     */
    public function loadArrayAction($object) {
        return true;
    }
	
    /**
     * @see Samus_ValidatorInterface::saveAction()
     *
     * @return boolean
     */
    public function saveAction($object) {
        return true;
    }
	
	/**
	 * @see Samus_ValidatorInterface::toStringAction()
	 */
    public function toStringAction($object) {
        return true;
    }
	
	/**
	 * @return string
	 */
    public function getErrorCssClass() {
        return $this->errorCssClass;
    }
	
	/**
	 * @return string
	 */
    public function getErrorMsg() {
        return $this->errorMsg;
    }
	
	/**
	 * @return string
	 */
    public function getSucessMsg() {
        return $this->sucessMsg;
    }
	
	/**
	 * @param string $errorCssClass
	 */
    public function setErrorCssClass($errorCssClass) {
        $this->errorCssClass = $errorCssClass;
    }
	
	/**
	 * @param string $errorMsg
	 */
    public function setErrorMsg($errorMsg) {
        $this->errorMsg = $errorMsg;
    }
	
	/**
	 * @param string $sucessMsg
	 */
    public function setSucessMsg($sucessMsg) {
        $this->sucessMsg = $sucessMsg;
    }

}