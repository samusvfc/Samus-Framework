<?php
/**
 * @author Vinicius Fiorio - samusdev@gmail.com
 * 
 */
abstract class Samus_Properties extends Samus_CRUD_Properties {

    /**
     * Altera o comportamento de empty() e isset() considerando FALSE e "" como
     * valores não vazios, use null para considerar vazio
     *
     * @param string $name
     * @return boolean
     * @todo testar esse metodo
     */
    public function  __isset($name) {
        if( ! is_callable( array($this,Samus_CRUD_MethodSintaxe::buildGetterName($property)) ) ) {
            throw new BadPropertyException($this, (string)$property);
        }
        $val = "";
        eval('$val=$this->' . Samus_CRUD_MethodSintaxe::buildGetterName($name) . "();");
        return empty($val);
    }
}