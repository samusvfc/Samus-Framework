<?php
/**
 * Classes que herdam de Samus_CRUD_Properties poderуo acessar diretamente as suas
 * propriedades privadas atravщs do seu setter e getter associado mas sem
 * declara-los diretamente.
 *
 * class Teste extends Samus_CRUD_Properties  {
 * 	private $email
 *
 *  public function getEmail() {
 *		return $this->email;
 *	}
 *
 *	public function setEmail($email) {
 *		$this->email = $email;
 *	 }
 * }
 *
 * $t = new Teste();
 * $t->email = "Samusdev@gmial.com";
 * echo $t->email;
 *
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version v 1.0.1
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category CRUD
 * @package CRUD
 *
 */
abstract class Samus_CRUD_Properties {

    /**
     * Obtem o valor de uma propriedade acessando getter associado
     *
     * @param $property
     * @return mixed|null Variable value
     */
    public function __get($property) {
        
        $getterName = Samus_CRUD_MethodSintaxe::buildGetterName($property);
        
        if(method_exists($this ,$getterName )) {
            $ref = new ReflectionMethod($this , $getterName );
            return $ref->invoke($this);
        } else {
            return null;
        }
    }

    /**
     * Especifica o valor de uma propriedade acessando o seu setter associado
     *
     * @param string $property
     * @param mixed $value
     */
    public function __set($property , $value) {
        $setter = Samus_CRUD_MethodSintaxe::buildSetterName($property);
        
        if(method_exists($this ,$setter)) {
            $ref = new ReflectionMethod($this , $setter );
            $ref->invoke($this , $value);
        } else {
            throw new BadMethodCallException("O metodo $setter nуo existe");
        }
    }

    


    /**
     * Trata a invocaчуo de mщtodos que nуo existem 
     * @param string $name
     * @param array $arguments
     */
    public function  __call($name, $arguments) {
        throw new BadMethodCallException("O mщtodo $name nуo existe");
    }


    /**
     * Altera o comportamento de empty() e isset() considerando FALSE e "" como
     * valores nуo vazios, use null para considerar vazio
     *
     * @param string $name
     * @return boolean
     * @todo testar esse metodo
     */
    public function  __isset($name) {
        $val = "";
        eval('$val=$this->' . Samus_CRUD_MethodSintaxe::buildGetterName($name) . "();");
    	return empty($val);
    }

}

?>