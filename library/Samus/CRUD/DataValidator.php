<?php
/**
 * Classe responsável por realizar as validações mais comuns, atenção ao método
 * validateFormat() que cria uma nova sintaxe de validaçõa
 *
 * @author Vinicius Fiorio - samusdev@gmail.com
 * @package CRUD
 */
class Samus_CRUD_DataValidator {

    /**
     * Tamanho máximo padrão para strings
     * @var string
     */
    public static $DEFAULT_MAX_LENGTH = 75;

    /**
     * Tamanho minimo padrão para strings
     * @var string
     */
    public static $DEFAULT_MIN_LENGTH = 0;


    /**
     * Valida se $data é um numero
     *
     * @param mixed $data
     * @return boolean
     */
    public static function validateNumericality($data) {
        $result = self::validateFloat($data);
        if(!$result) {
            $result = self::validateInt($data);
        }
        return $result;
    }

    /**
     * Valida se $data é um numero inteiro
     * @param mixed $data
     * @return boolean
     */
    public static function validateInt($data) {
        if(filter_var($data , FILTER_VALIDATE_INT)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Valida se $data é um float
     * @param mixed $data
     * @return boolean
     */
    public static function validateFloat($data) {
        if( filter_var($data , FILTER_VALIDATE_FLOAT)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Valida se um numero esta em uma faixa de caracteres
     *
     * @param mixed $data
     * @param ing|float $min
     * @param int|float $max
     * @return boolean
     */
    public static function validateNumberRange($data , $min , $max) {
        if(!self::validateNumericality($data)) {
            return false;
        }

        if($data >= $min and $data <= $max) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Valida se data tem algum valor
     * @param mixed $data
     * @return boolean
     */
    public static function validatePresence($data) {
        return empty($data);
    }

    /**
     * Valida se $data é um email
     * @param string $data
     * @return boolean
     */
    public static function validateEmail($data) {
        $resultado = filter_var($data, FILTER_VALIDATE_EMAIL);
        if($resultado)
        return true;
        else
        return false;
    }

    /**
     * Valida o tamanho de uma string
     * @param string $data
     * @param int $min
     * @param int|null $max
     */
    public static function validateStringLength($data , $min=null , $max=null) {

        if($max==NULL) {
            $max = self::$DEFAULT_MAX_LENGTH;
        }

        if($min == null) {
            $max = self::$DEFAULT_MIN_LENGTH;
        }

        $length = strlen($data);

        if($length >= $min && $length <= $max) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * Valida se data é uma string de a até Z
     * @param mixed $data
     * @return boolean
     */
    public static function validateAToZ($data) {
        $data = strtolower($data);
        return ereg("[a-z]", $data);
    }

    /**
     * Valida um formato aleatório de string, bastando imformar em $format o
     * formato desejado que segue a sintaxe abaixo:<br/>
     * A - Qualquer caracterer de a-z
     * 9 - qualquer número
     * _ - (underline) qualquer caracter
     * Outros - qualquer outro caracter será validado
     * Ex:
     * echo Samus_Validator::validateFormat($data , "AAA999-33");
     * valida uma strig que comece por 3 caractéres de a à z, seguido de 3 numeros
     * que tenha um ifem na sétima posição e termine com dois numeros
     *
     * @param mixed $data
     * @param string $format "A" ou "9" ou "_"
     * @param boolean $randomSize
     * @return
     */
    public static function validateFormat($data , $format="A9" , $randomSize = true) {
        $dataArray = str_split($data);

        $format = strtolower($format);

        $ai = new ArrayIterator(str_split($format));

        // se não tiver um tamanho aleatorio valida o tamanho de format

        if(!$randomSize) {
            if(strlen($data) != strlen($format)) {
                return false;
            }
        }

        foreach($dataArray as $d) {
            $validFormat = $ai->current();
            if(!$ai->valid()) {
                break;
            }

            // valida se é uma string entre A e Z
            if($validFormat=="a") {
                if(!self::validateAToZ($d)) {
                    return false;
                    break;
                }
            } elseif($validFormat=="9") { // valida se é um numero
                
                if(!is_numeric($d)) {
                    return false;
                    break;
                }
            } elseif($validFormat!="_") { // valida outros caracteres
                if(strtolower($d) != strtolower($validFormat)) {
                    return false;
                    break;
                }
            }

            $ai->next();
        }

        return true;

    }

    /**
     * Valida um CEP brasileiro
     * @param mixed $data
     * @param string $format
     * @return boolean
     */
    public static function validateBrazilCEP($data , $format="99999-999") {
        return self::validateFormat(trim($data), $format);

    }

    /**
     * Valida um Telefone no formato brasileiro
     * @param mixed $data
     * @param string $format
     * @return boolean
     */
    public static function validateBrazilTel($data , $format="(99) 99999-9999") {
        return self::validateFormat($data, $format);
    }

    /**
     * Valida se um atributo de um objeto CRUD é unico na sua tabela
     *
     * @param DAO $daoObject
     * @param string $propertieName
     * @param mixed $data
     * @return boolean
     */
    public static function validateCRUDUniqueness($daoObject , $propertieName , $data) {
        $p = new Model_Cms_Pagina();
         
        $p->getDao()->setAtributes('id' , $propertieName);
         
        if(self::validateNumericality($data)) {
            $values = $p->getDao()->find("$propertieName=$data");
        } else {
            $values = $p->getDao()->find("$propertieName='$data'");
        }
         
        if(empty($values)) {
            return true;
        } else {
            return false;
        }
    }

}
?>