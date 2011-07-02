<?php

/**
 * A Classe Samus_Model � respons�vel por analizar a classe e ligar ela com a camada de
 * persist�ncia de dados, ela � capaz de criar as tabelas que representam as
 * modelos ("modelo" ou "Samus_Model" representa uma classe de Modelo do Curral. Todas as
 * classes de modelo devem extender a Samus_Model "class Modelo extends Samus_Model()".
 * <br />\n
 * <br />\n
 * Os PHPDoc dos atributos s�o parte do c�digo, a sintaxe � a mesma do PHPDoc
 * normal utilizado, a diferen� � que depois da declara��o do tipo da vari�vel
 * deve ser especificado o tipo de dado da coluna na tabela (simples n�o) e o
 * nome do atributo ser� o nome da coluna. O nome da tabela criada � o nome do
 * atributo 'name' do PHPDoc, espa�os e caracteres especiais s�o removidos.
 * <br />\n
 * Diferente de outros framework que usam conven��es obscuras, a id�ia � manter
 * claro as coisas que o framework esta fazendo por tr�s do c�digo
 * <br />
 * <br />
 * Associa��es 1 para 1 s�o feitas seguindo o Padr�o CRUD (veja na documenta��o)
 *
 *
 *
 * \@var string varchar(120)
 * private $variavel;
 *
 *
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @package Samus
 */
abstract class Samus_Model extends Samus_CRUD_DAO {

    public $_co = null;
    public $_tableName;

    public function __construct($id=null) {
        parent::__construct($id);
    }

    public function get_tableName() {
        return $this->tableName();
    }

    /**
     * Obtem o nome da tabela do objeto atual
     * @return string
     */
    public function tableName() {
        $p = new ReflectionClass($this);
        $this->_tableName = Samus_CRUD::getTableNameFromClassName($p->getName());
        return $this->_tableName;
    }

    /**
     * Obtem o nome da classe
     * @return string
     */
    public function get_className($lastName=true) {
        if ($lastName) {
            $p = new ReflectionClass($this);
            $a = explode("_", $p->getName());
            return $a[count($a) - 1];
        } else {
            return $p->getName();
        }
    }

    /**
     * Obtem um nome de atributo qualificado (incluindo o nome da tabela)
     * @param string $name
     */
    public function attr($name) {
        return $this->tableName() . '.' . $name;
    }

    /**
     *
     * @return Samus_CRUD_DAOModel
     */
    public function getDao() {
        return parent::getDao();
    }

    /**
     * @return Samus_CO
     */
    public function getCO() {

        $className = $this->getDao()->getClassName() . Samus_ModelController::DEFAULT_CO_SUFIX;

        if ($this->_co instanceof $className) {
            return $this->_co;
        } else {

            if (class_exists($className, true)) {
                $evalStr = '$this->_co = new '.$className.'($this);';
                eval($evalStr);
                return $this->_co;
            } else {
                throw new Samus_CRUD_CRUDException("A classe $className n�o existe, verifique se o nome esta correto ou se ela existe", "");
            }
        }
    }

    /**
     * Define se o objeto ja esta carregado,
     * Retorna falso caso o atributo $id possua algum valor
     *
     * @return boolean
     */
    public function isEmpty() {
        return empty($this->id);
    }

    public function get_co() {
        return $this->_co;
    }

    public function set_co($_co) {
        $this->_co = $_co;
    }

}