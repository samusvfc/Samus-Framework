<?php
require_once 'Samus/types/Type_Number.php';
require_once 'Samus/types/Type_Array.php';
require_once 'Samus/types/Type_Boolean.php';
require_once 'Samus/types/Type_String.php';

/**
 * @author Vinicius Fiorio - samusdev@gmail.com
 */
class Typer {

    /**
     *
     * @param mixed $var
     * @return Type_Boolean|Type_Number|Type_String|Type_Array
     */
    public static function typeValue($var) {
        if(is_numeric($var) || $var instanceof  Number) {

            return new Type_Number($var);

        } elseif(is_string($var) || $var instanceof Str) {

            return new Type_String($var);

        } elseif(is_array($var)) {

            return new Type_Array($var);

        } elseif(is_bool($var)) {

            return new Type_Boolean();
            
        } else {
            return $var;
        }

    }

}
?>
