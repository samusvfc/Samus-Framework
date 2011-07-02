<?php
require_once 'Samus/types/DataTypeInterface.php';
require_once 'Samus/types/Date_Time.php';

/**
 * Implementaчуo do tipo primitibvo string, adicionando todos os mщtodos mais 
 * comuns
 *
 * @author Vinicius Fiorio - samusdev@gmail.com
 */
class Type_DateTime extends DateTime implements DataTypeInterface {
	
    public $date;
	
	/**
	 * @see DataTypeInterface::getType()
	 *
	 * @return string
	 */
    public function getType() {
        return "Date_Time";
    }

    /**
     * Obtem uma instance de Date_Time
     * @param string $date
     * @return Date_Time
     */
    public static function getInstance($date=null) {
        return new Date_Time($date);
    }
	
    public function __construct($date=null , $format="y/m/d H:i:s") {
        if(empty($date)) {
            $this->date = $date;
        } else {
            $this->date = date($format);
        }
		
    }
    

	
    public function validateData($year , $month , $day) {
        return checkdate($month , $day , $year);
    }

    public function __tostring() {
        return $this->date;
    }

    public static function cast($object) {
        if($object instanceof Date_Time) {
            return $object;
        } else {
            return new Date_Time((string) $object);
        }
    }
	

}
?>