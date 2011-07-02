<?php

/**
 * Executa a��o para valida��o, de objetos conforme os tipos de dados
 * especificados
 *
 * @author Vinicius Fiorio Cust�dio - samusdev@gmail.com
 * @package Samus_CRUD
 * @todo tem que colocar pra funcionar essa classe, ela ja esta avaliando os tipos
 * de dados e executando os m�todos validates mas tem que colocar as valida��es corretas
 */
class Samus_CRUD_RequestValidator {

    /**
     * Objeto que ser� analisado
     * @var object
     */
    private $object;

    /**
     * @var array
     */
    private $requiredProperties = array();

    /**
     * Array que casa a propriedade com seu tipo de dado no MySql
     * @var array
     */
    private $propertieType = array();

    /**
     * Resultado das valida��es
     * @var array um array com o resultado da valida��o
     */
    private $resultArray = array();

    const CSS_ERROR_CLASS = 'request-error';

    const DEFAULT_ERROR_MSG = "Preencha corretamente: ";

    private $initCheck = false;

    public function __construct($object) {
        $this->setObject($object);
    }

    /**
     * Inicia a anlise de um objeto
     * @return void
     */
    public function init() {

        $ref = new ReflectionClass($this->object);

        $parentClassesArray = array($ref);

        while ( $ref->getParentClass()->getName() != Samus_CRUD::getTopLevelClass()) {
            $ref = $ref->getParentClass();
            $parentClassesArray[] = $ref;
        }

        $parentClassesArray = array_reverse($parentClassesArray);

        foreach ($parentClassesArray as $ref) {
            /*@var $ref ReflectionClass */

            foreach($ref->getProperties() as $prop) {
                /* @var $prop ReflectionProperty */
                $this->readDocComment($prop);
            }

        }

        $this->initCheck = true;

    }

    /**
     * Obtem o resultado da an�lise das classes, deve ser chamado ap�s a utiliza��o
     * de $requestValidator->init()
     *
     * @return array
     */
    public function result() {
        $str = "";

        $cont=0;



        foreach($this->resultArray as $key => $value) {
            if(!$value[0]) {

                $str .= self::DEFAULT_ERROR_MSG;
                $str .= " <strong>";
                $str .= $key;
                $str .= "</strong>";
                $str .= " (" . $value[1] . ")";
                $str .= "<br />";
                  ++$cont;
            }
          
        }

        if($cont!=0) {
            $str = "<div class='".self::CSS_ERROR_CLASS."'>" . $str;
            $str .= "</div>";
        }

        return $str;
    }

    /**
     * Simplesmente valida um objeto e n�o exibe a mensagem de erro, n�o necessita
     * de utilizar init() antes
     * @return boolean
     */
    public function valid() {
        if($this->initCheck) {
            if(empty($this->resultArray)) {
                return true;
            } else {
                return false;
            }
        } else {
            $this->init();
            return $this->valid();
        }
    }


    /**
     * Faz a leitura de um bloco de coment�rio de uma propriedade
     * @return array
     */
    private function readDocComment(ReflectionProperty $propertie) {

        $docComment = $propertie->getDocComment();

        $docComment = strstr($docComment , "@var");
        if (! empty($docComment)) {

            $docComment = str_replace("/" , "" , $docComment);
            $docComment = str_replace("*" , "" , $docComment);
            $docComment = str_replace("@var" , "" , $docComment);
            $docComment = trim($docComment);
            $docComment = strtolower($docComment);

            //verifica se � not null
            $isNotNull = strstr($docComment , "not null");
            $isNotNullResult = true; // variavel de controle para validar se foi executado o notnull
            if($isNotNull) {
                $isNotNullResult = $this->validateNull($propertie);
                $this->resultArray[$propertie->getName()] = array($isNotNullResult , 'null');
            }
            if($isNotNullResult) {

                //verifica o tipo de dado
                $tipo = $this->getDataTypeFromDocComment($propertie);

                //pego o valor da propiedade no obeto
                $value = null;
                $strEval = '$value = $this->object->' . $propertie->getName() . ';';
                 
                eval($strEval);

                $this->resultArray[$propertie->getName()] = array($this->validate($propertie , $tipo , $docComment) , $value );
            }
        }
    }

    /**
     * Obtem o tipo de dado no MySql de um coment�rio de uma propriedade, define
     * se � Num�rico, String, Data, Geom�trico
     * @param $doc string bloco de coment�rio
     * @return string nome do tipo de dado
     */
    private function getDataTypeFromDocComment(ReflectionProperty $propertie) {
        $tipo = "undefined";
        $temTipo = false;

        $doc = $propertie->getDocComment();

        foreach(Samus_Database_MysqlDataTypes::getAllTypesArray() as $type) {
            $temTipo = stristr($doc , $type);
            if($temTipo) {
                $tipo = $type;
                break;
            }
        }
         
        if($tipo != 'undefined') {
            $tipo = Samus_Database_MysqlDataTypes::getTypeFromType($tipo);
        }

        return $tipo;

    }

    /**
     * Realiza a valida��o de uma propriedade
     * @param $propertie
     * @param $tipo
     * @return boolean
     */
    private function validate(ReflectionProperty $propertie , $tipo , $docComment="") {

        if($tipo == Samus_Database_MysqlDataTypes::STRING_TYPE) {

            $result = $this->validateString($propertie);

        } elseif($tipo == Samus_Database_MysqlDataTypes::NUMERIC_TYPE) {

            $result = $this->validateNumeric($propertie);

        } elseif($tipo == Samus_Database_MysqlDataTypes::DATE_TYPE) {

            $result =  $this->validateDate($propertie);

        } elseif($tipo == Samus_Database_MysqlDataTypes::VARCHAR_TYPE) {

            $result =  $this->validateVarchar($propertie , $docComment);

        } elseif($tipo == Samus_Database_MysqlDataTypes::BOOLEANS_TYPE) {

            $result = $this->validateBoolean($propertie);

        } else {

            $result = $this->validateUndefined($propertie);

        }
        return $result;
    }

    /**
     * Executa a a��o para parametros nulos
     * @param $prop
     * @return boolean
     */
    protected function validateNull(ReflectionProperty $prop) {
        $value = null;
        $strEval = '$value = $this->object->' . $prop->getName() . ';';
         
        eval($strEval);

        if(is_null($value) || empty($value)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Executa a a��o para dados num�ricos
     * @param ReflectionProperty $prop
     * @return boolean
     */
    protected function validateNumeric(ReflectionProperty $prop) {
        $value = null;
        $strEval = '$value = $this->object->' . $prop->getName() . ';';
         
        eval($strEval);

        // se for um objeto for�o o __tostring
        if(is_object($value)) {
            $value = (string) $value;
        }

        if(empty($value)) {
            return true;
        }


        if(is_numeric($value)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Executa a a��o para propiedades do tipo string
     * @param ReflectionProperty $prop
     * @return boolean
     */
    protected function validateString(ReflectionProperty $prop) {
        $value = null;
        $strEval = '$value = $this->object->' . $prop->getName() . ';';
         
        eval($strEval);

        if(is_string($value)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Executa a��o para tipos de dados Data, trata como true qualquer data no
     * formato
     * dd/mm/aaaa
     * dd-mm-aaaa
     * aaaa/mm/dd
     * @param $prop
     * @return true
     */
    protected function validateDate(ReflectionProperty $prop) {
        $value = null;
        $strEval = '$value = $this->object->' . $prop->getName() . ';';
         
        eval($strEval);

        if(empty($value)) {
            return true;
        }

        // validao formato da data depois valida se � uma data esta num per�odo v�lido
        if(Samus_CRUD_DataValidator::validateFormat($value , "9999-99-99" , true)) {

            $data = array(
            'ano' => substr($value , 0 , 4) ,
            'mes' => substr($value , 5 , 2) ,
            'dia' => substr($value , 8 , 2)
            );
            return checkdate($data['mes'] , $data['dia'] , $data['ano']);

        } elseif(Samus_CRUD_DataValidator::validateFormat($value , "99-99-9999" , true)) {

            $data = array(
            'ano' => substr($value , 6 , 4) ,
            'mes' => substr($value , 3 , 2) ,
            'dia' => substr($value , 0 , 2)
            );

            return checkdate($data['mes'] , $data['dia'] , $data['ano']);

        } elseif(Samus_CRUD_DataValidator::validateFormat($value , "99/99/9999" , true)) {

            $data = array(
            'ano' => substr($value , 6 , 4) ,
            'mes' => substr($value , 3 , 2) ,
            'dia' => substr($value , 0 , 2)
            );

            return checkdate($data['mes'] , $data['dia'] , $data['ano']);
        } else {
            return false;
        }

    }

    /**
     * Executa a��o para tipos de dados indefinidos
     * @param ReflectionProperty $prop
     * @return boolean
     */
    protected function validateUndefined(ReflectionProperty $prop) {
        return true;
    }

    /**
     * Executa a��o para tipos de dados booleanos
     * @param ReflectionProperty $prop
     * @return boolean
     */
    protected function validateBoolean(ReflectionProperty $prop) {
        $valor = null;
        $strEval = '$valor = $this->object->' . $prop->getName() . ';';
         
        eval($strEval);

        if(empty($valor)) {
            return true;
        }

        if($valor == true || $valor == false || $valor== 1 || $valor == 0 || $valor == 2 ) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Executa a a��o para tipos de dados VARCHAR
     * @param ReflectionProperty $prop
     * @return boolean
     */
    protected function validateVarchar(ReflectionProperty $prop , $docComment) {

        $tamanho = filter_var($docComment , FILTER_SANITIZE_NUMBER_INT);
        $valor = null;
        $strEval = '$valor = $this->object->' . $prop->getName() . ';';
         
        eval($strEval);

        if(empty($valor)) {
            return true;
        }

        if(Samus_CRUD_DataValidator::validateStringLength($valor , 1 , $tamanho)) {
            return true;
        } else {
            return false;
        }

    }



    /**
     *
     * @return object
     */
    public function getObject() {
        return $this->object;
    }

    /**
     *
     * @param $object object
     */
    public function setObject($object) {
        $this->object = $object;
    }


    public function getResultArray() {
        return $this->resultArray;
    }

    public function setResultArray($resultArray)
    {
        $this->resultArray = $resultArray;
    }
}


?>