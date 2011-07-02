<?php

/**
 * Samus_CRUD_DAO - Dynamic Acess Object
 *
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version v 1.0.1
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category Samus_CRUD
 * @package Samus_CRUD
 * @abstract
 */
abstract class Samus_CRUD_DAO extends Samus_CRUD_Properties implements Samus_CRUD_DAOInterface, Samus_CRUD_ModelRulesInterface {
    /**
     * Nome da coluna que contem o ID da entidade
     * @var string
     */
    const PRIMARY_KEY_NAME = "id";

    /**
     * @var int INTEGER(11) auto_increment
     */
    protected $id;
    /**
     * @var string
     */
    private static $dbTable;
    /**
     * Instancia do Samus_CRUD_DAO responsável por todas as operações
     * @var Samus_CRUD_DAOModel
     */
    private $dao = null;

    /**
     * Construtor de um Objeto Samus_CRUD_DAO, se informado ID será carregado a instancia
     * com o ID especificado.
     *
     *  Caso Samus_CRUD_TableFactory estiver ativado fara a verificação da existência da
     *  tabela da entidade no BD.
     *
     * @param $id
     * @return unknown_type
     */
    public function __construct($id = null) {

        if ($this->constructRule()) {

            if (Samus_CRUD_TableFactory::isCreateTablesEnabled()) {
                $tf = new Samus_CRUD_TableFactory($this);
            }

            if (!empty($id)) {
                $this->getDao()->load((int) $id);
            }
        } else {
            return false;
        }
    }

    /**
     * Constroi o nome da classe dentro do padrão Samus_CRUD
     * letras maiusculas)
     *
     * @param string $className nome da tabela
     */
    public function buildTableName($className) {
        return Samus_CRUD::getTablePrefix() . Util_String::upperToUnderline($className);
    }

    /**
     * Obtem o nome da table a partir do nome da classe, o nome da classe pode
     * ser dado tbm por um objeto
     * @param string $className
     * @return string
     */
    public static function getTableNameFromClassName($classNameOrObject) {

        if (is_object($classNameOrObject)) {
            $className = get_class($classNameOrObject);
        } else {
            $className = $classNameOrObject;
        }

        return Samus_CRUD::getTableNameFromClassName($className);
    }

    /**
     * Alias para  getTableNameFromClassName
     * @param  string $className
     * @return string
     */
    public static function table($className) {
        return self::getTableNameFromClassName($className);
    }

    /**
     * @see Persistent::getDbTable()
     * @return string nome da tabela especificada
     */
    public static function getDbTable() {
        return self::$dbTable;
    }

    /**
     *
     *
     * @param string $tableName nome da tabela
     */
    protected static function setDbTable($tableName) {
        self::$dbTable = $tableName;
    }

    /**
     * Inicia o objeto Samus_CRUD_DAO, qualquer método criado passa por este método
     * @return void
     */
    private function buildDAO() {
        if ($this->dao == null) {
            $this->dao = new Samus_CRUD_DAOModel();
            $this->dao->setObject($this);
        }
    }

    /**
     * Obtem o Samus_CRUD_DAO correspondente
     * @return Samus_CRUD_DAOModel
     */
    public function getDao() {
        $this->buildDAO();
        return $this->dao;
    }

    /**
     * @return Samus_CRUD_DAOModel
     */
    public function get_dao() {
        return $this->getDao();
    }

    /**
     * Obtem a chave primária
     * @return int
     */
    public function getId() {
        return (int) $this->id;
    }

    /**
     * Seta o a chave primaria
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Obtem uma instancia do objeto Samus_CRUD_DAO,
     *
     * @param string $className
     * @param array $args
     * @return object
     */
    public static function getInstance($className = "", $args = array()) {
        $ref = new ReflectionClass($className);
        return $ref->newInstance($args);
    }

    /**
     * Obtem o ID
     * @return string
     */
    public function __toString() {
        return (string) $this->getId();
    }

    public function set($propertie, $value=null) {
        if (is_array($propertie)) {

            foreach ($propertie as $k => $val) {
                $str = '$this->' . Samus_CRUD_MethodSintaxe::buildSetterName($k);
                if (is_string($val)) {
                    $str .= '("$val");';
                } else {
                    $str .= '($val);';
                }

                eval($str);
            }
        } else {
            $strEval = '$this->' . $propertie . '=$value;';
            eval($strEval);
        }
    }

    /**
     * Regra aplicada à instanciação de objetos, se TRUE retorna a instancia
     * normalmente senão retorna false
     *
     * @return boolean
     */
    public function constructRule() {
        return true;
    }

    /**
     * Regra aplicada aos métodos de salvamento
     *
     * @return boolean
     */
    public function saveRule() {
        return true;
    }

    /**
     * Regra aplicada aos métodos de carregamento e carregamento de listas
     * @return boolean
     */
    public function loadRule() {
        return true;
    }

    /**
     *
     * @return boolean
     */
    public function deleteRule() {
        return true;
    }

    /**
     * Armazena todos os campos Extras (fora do modelo) que podem resultar de uma query
     * de vários modelos
     * @var array
     */
    protected $_extraValues = null;

    /**
     * Adiciona um valor ao objeto de valores extras
     * @param string $name
     * @param mixed $value
     */
    public function addExtraValue($className, $name, $value) {

        $str = ('

if(!isset($this->_extraValues->' . $className . ') ) {
            $c = new ' . $className . '();
        } else {
            $c = $this->_extraValues->' . $className . ';
        }
');

        if (is_string($value)) {
            $str .= '$c->' . Samus_CRUD_MethodSintaxe::buildSetterName($name) . '("' . $value . '");';
        } else {
            $str .= '$c->' . Samus_CRUD_MethodSintaxe::buildSetterName($name) . '(' . $value . ');';
        }

        $str .= '$this->_extraValues->' . ($className) . ' = $c;';
        eval($str);
        return $this;
    }

    /**
     * Obtem um valor do objeto de valores extras
     *
     * @param mixed $name
     * @return string
     */
    public function getExtraValue($className, $name) {
        $val = null;
        eval('$val = $this->_extraValues->' . ucfirst($className) . '->' . $name . ';');
        return $val;
    }

    /**
     * Obtem o objeto de valores extras
     * @return stdClass
     */
    public function getExtra($className=null) {
        if (empty($className)) {
            return $this->_extraValues;
        } else {
            $v = null;
            eval('$v = $this->_extraValues->' . $className . ';');
            return $v;
        }
    }

    public function setExtra($extraValues) {
        $this->_extraValues = $extraValues;
    }

}