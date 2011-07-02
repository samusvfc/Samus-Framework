<?php
require_once 'Samus/types/DataTypeInterface.php';
/**
 * @author Vinicius Fiorio - samusdev@gmail.com
 */
class Type_Boolean implements DataTypeInterface {
    
    public $boolean = false;

    public function  __construct($value=false) {
        $this->boolean = $value;
    }

    /**
     * Obtem uma instancia de Boolean
     * @return Boolean
     */
    public static function getInstance($value=false) {
        return new Boolean($value);
    }

    public  function getType() {
        return "Boolean";
    }

    /**
     * Obtem uma string
     * @return string
     */
    public function getString() {
        return (string) $this;
    }

    /**
     * Obtem um inteiro como booleano
     * @return int
     */
    public function getInt() {
        if($this->boolean) {
            return 1;
        } else {
            return 0;
        }
    }

    public function  __toString() {
        if($this->boolean) {
            return "true";
        } else {
            return "false";
        }
    }


    /**
     * Retorna um boolean
     * @param Boolean $object
     * @return Boolean
     */
    public static function cast($object) {
        if($object instanceof Boolean) {
            return $object;
        } else {
            return new Boolean((boolean) $object);
        }
    }



}
?>
