<?php

/**
 * -----------------------------------------------------------------------------
 * ATENÇÃO! ####################################################################
 * Este módulo utiliza os comentários para criação de código, por isso qualquer
 * extensão que acelere o php eliminando o Cache do PHP fara com que esta classe
 * não funcione corretamente, o mais comum é a extensão encontrada na linha:
 *
 * zend_extension = "C:\xampp\php\ext\php_eaccelerator.dll"
 * -----------------------------------------------------------------------------
 *
 * Cria as tabelas do padrão Samus_CRUD, os comentários dos atributos das classes devem
 * especificar além do tipo de dado no php o tipo de dado no banco
 *
 * Ex.:
 * string VARCHAR(125) NOT NULL
 * int INTEGER(10) NOT NULL
 *
 * FKs também são geradas automáticamente para cada objeto aninhado na classe
 *
 * People INTEGER(10)
 * private $people;
 *
 * Atenção: Alguns acertos finais deverão ser feitos após a criação da tabela
 *
 * - adicionada a funcionalidade de criação de tabelas com varias chaves primarias
 * bastando informar primary key no comentário da propriedade
 *
 * - 27/05/2009 - Adicionada a funcionadade de especifcação dos parametros das
 * FKs através das constantes ON_DELETE on ON_UPDATE
 *
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version v 1.6.0 25/08/2008
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category Samus_CRUD
 * @package Samus_CRUD
 */
class Samus_CRUD_TableFactory extends Samus_Object {

    /**
     * Define se será feita uma avaliação da classe para verificar a existência
     * da tabela correspondente
     * @var boolean
     */
    private static $createTables = false;
    /**
     * Nome da tabela da entidade
     * @var string
     */
    private static $dbTable;
    /**
     * Especifica se a tabela da entidade deve ser excluida e criada novamente
     * @var boolean
     */
    private $dropToCreate = false;
    /**
     * Um Objeto para analise
     * @var DAO|string
     */
    private $DAO_Object;
    /**
     * Uma array que faz o controle das Primary Keys para correta criação  de
     * chaves candidatas
     * @var array
     */
    private $pkArray = array();
    public static $msg;

    const PRIMARY_KEY_NAME = "id";

    /**
     * Nome da constante que terá os parametros ON_DELETE
     */
    const ON_DELETE_CLASS_CONST = "ON_DELETE";
    /**
     * Nome da constante que tem os parâmetros do ON_UPDATE
     */
    const ON_UPDATE_CLASS_CONST = "ON_UPDATE";

    /**
     * @var string
     */
    private $columPrefix;

    /**
     * Se informado o ID do objeto, será carregado automaticamente os dados
     * do objeto indicado;
     * @param string|int $id
     */
    public function __construct($DAO_Object) {
        $this->DAO_Object = $DAO_Object;
        $this->analyseClass($DAO_Object);
    }

    /**
     * Sobrescreve a função strstr() do Core do PHP para uso específico na classe
     * retornando corretamente a string depois da string encontrada
     *
     * @param string $haystack
     * @param string $needle
     * @param boolean $before_needle
     * @return string
     */
    private function strstr($haystack, $needle, $before_needle = FALSE) {
        if (($pos = strpos($haystack, $needle)) === FALSE)
            return FALSE;

        if ($before_needle)
            return substr($haystack, 0, $pos + strlen($needle));
        else
            return substr($haystack, $pos);
    }

    /**
     * Esta classe é responsável por analizar a classe filha, a partir desse metodo
     * ele popula os atributos $dbColumns com um array de dados para montar a tabela
     * e monta o nome da tabela
     */
    private function analyseClass($classObject = null) {

        if ($classObject == null) {
            $classObject = $this;
        }

        $ref = new ReflectionClass($classObject);

        //o nome da tabela é o nome da classe com underlines no lugar das maiusculas
        $tableName = $this->buildTableName($ref->getName());

        self::setDbTable($tableName);

        if (self::$createTables) {

            $this->buildTableColumns($classObject);

            try {

                $r = Samus_CRUD_CRUDQuery::executeQuery("SELECT 1 FROM $tableName");
            } catch (Samus_CRUD_CRUDQueryException $ex) {
                $this->createTable();
            }

            //mysqli_query(Samus_CRUD::getConn() , "SELECT 1 FROM $tableName");
        }
    }

    /**
     * Especifica as colunas da tabela
     *
     * @param mixed|string $classObject
     */
    private function buildTableColumns($classObject = null) {
        if ($classObject == null)
            $classObject = $this;

        $dbColumns = array();
        $ref = new ReflectionClass($classObject);

        $properties = $ref->getProperties();

        foreach ($properties as $propriedade) {
            $doc = $propriedade->getDocComment();

            if (!empty($doc)) {
                $doc = strstr($doc, "@var");
                if (!empty($doc)) {

                    $doc = str_replace("/", "", $doc);
                    $doc = str_replace("*", "", $doc);
                    $doc = str_replace("@var", "", $doc);
                    $doc = trim($doc);

                    // se encontrar alguma chave primária no comentário guarda em um array para adicionar depois no momento da criação
                    if (strpos(strtolower($doc), "primary key")) {
                        $this->pkArray [] = $propriedade->getName();
                        $doc = str_replace("primary key", "", strtolower($doc));
                    }

                    $parametrosArray = explode(" ", $doc);

                    array_unshift($parametrosArray, $propriedade->getName());

                    if ($propriedade->getName() == self::PRIMARY_KEY_NAME) {
                        array_unshift($dbColumns, $parametrosArray);
                    } else {
                        $dbColumns [] = $parametrosArray;
                    }
                }
            }
        }

        $this->pkArray = array_unique($this->pkArray);

        $tableName = $this->buildTableName($ref->getName());
        $prefix = $this->buildAttrPrefix($tableName) . '_';

        /*
          foreach ($dbColumns as $key => $c) {
          $dbColumns[$key][0] = $prefix. $dbColumns[$key][0];
          }
         */
        return $dbColumns;
    }

    /**
     * Cria a tabela no banco conforme os dados da tabela, se vc precisa alterar a estrutura
     * da tabela, ou use um Gerenciador de BD para alterála (lembre-se de atualizar
     * o PHPDoc)
     *
     */
    public function createTable($topLevelClass = "", $onDelete = "SET NULL", $onUpdate = "CASCADE") {
        $pkStr = "";
        if (empty($topLevelClass)) {
            $topLevelClass = Samus_CRUD::getTopLevelClass();
        }

        $sqlToSave = "";

        $ref = new ReflectionClass($this->DAO_Object);

        $parentClassesArray = array($ref);

        while ($ref->getParentClass()->getName() != $topLevelClass) {
            $ref = $ref->getParentClass();
            $parentClassesArray [] = $ref;
        }

        $parentClassesArray = array_reverse($parentClassesArray);

        foreach ($parentClassesArray as $ref) {

            //caso tenha superClasses


            $superTableName = $this->buildTableName($ref->getName());
            /*             * *****************************************************************
             * GERAÇÃO DE SUPER-TABELAS
             * gera as tabelas das classes pais
             * **************************************************************** */
            $sql = "";
            $sql .= "CREATE TABLE IF NOT EXISTS `" . Samus_Database_Connection::getDataBaseName() . "`.`" . $superTableName . "` (";

            $fks = "";

            $attrPrefix = $this->buildAttrPrefix($superTableName);

            foreach ($this->buildTableColumns($ref->getName()) as $column) {
                if (count($column) <= 2)
                    continue;

                $sql .= "`" . $column [0] . "` " . $column [2] . " ";

                $cont = 0;
                foreach ($column as $columnDetail) {

                    if ($cont > 2) {
                        $sql .= $columnDetail . " ";
                    }
                    ++$cont;
                }

                if (class_exists($column [1])) {

                    try {

                        $onDeleteConst = $ref->getConstant(self::ON_DELETE_CLASS_CONST);
                        $onUpdateConst = $ref->getConstant(self::ON_UPDATE_CLASS_CONST);

                        if ($onDeleteConst) {
                            $onDelete = $onDeleteConst;
                        }

                        if ($onUpdateConst) {
                            $onUpdate = $onUpdateConst;
                        }
                    } catch (Exception $ex) {
                        
                    }

                    $fks .= ", CONSTRAINT `fk_" . $ref->getName() . "_" . $column [0] . "`
					    FOREIGN KEY (`" . $column [0] . "` )
					    REFERENCES `" . Samus_Database_Connection::getDataBaseName() . "`.`" . $this->buildTableName($column [1]) . "` (`id` )
					    ON DELETE $onDelete
					    ON UPDATE $onUpdate";
                }

                $sql .= " ,
";
            }

            // varre o array de chaves primárias para criar abaixo
            if (!empty($this->pkArray)) {

                $pkStr = ",";
                foreach ($this->pkArray as $pk) {
                    $pkStr .= "`$pk`, ";
                }
                $pkStr = substr($pkStr, 0, - 2);
            }

            $sql .= " PRIMARY KEY  (`" . self::PRIMARY_KEY_NAME . "`$pkStr)";

            if (!empty($fks)) {
                $sql .= $fks;
            }

            if ($ref->getParentClass() && $ref->getParentClass()->getName() != $topLevelClass) {
                //caso tenha uma classe pai faz as FK


                $sql .= "
				  , CONSTRAINT `fk_" . $ref->getParentClass()->getName() . "_" . $ref->getName() . "`
				    FOREIGN KEY (`id` )
				    REFERENCES `" . Samus_Database_Connection::getDataBaseName() . "`.`" . $this->buildTableName($ref->getParentClass()->getName()) . "` (`id` )
				    ON DELETE CASCADE
				    ON UPDATE CASCADE
			";
            }

            $sql .= ") ENGINE=" . Samus_Database_Connection::getEngine() . " DEFAULT CHARSET=" . Samus_Database_Connection::getCharset() . ";";

            //mysqli_query(Samus_CRUD::getConn() , $sql);
            $r = Samus_CRUD::executeQuery($sql);

            if (!$r) {
                self::$msg = "<h1>A tabela: " . $this->getDbTable() . " não pode ser criada</h1>";
                self::$msg = "<code>" . $sql . "<c/ode>";
            } else {

                self::$msg = 'Tabela "' . $superTableName . '" Criada com sucesso ! <br />';
            }
            $sqlToSave .= $sql . "\n\n";

            self::$msg = $sqlToSave;
        }



        flush();
        $this->writeCreatesSql($sqlToSave);
    }

    /**
     * Cria um arquivo com todos os SQLs executados
     *
     * @param string $sqlText sql para ser escrita
     * @param string $filename nome do arquivo que será salvo
     */
    private function writeCreatesSql($sqlText, $filename = "__creates.sql") {

        $filename = WEB_DIR . "system/" . $filename;

        if (!$handle = fopen($filename, 'a')) {
            throw new Exception("O arquivo não pode ser aberto");
        }

        if (is_writable($filename)) {
            if (fwrite($handle, $sqlText) === FALSE) {
                throw new Exception("Não foi possível escrever no arquivo ($filename)");
            }

            fclose($handle);
            return true;
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
        $a = explode('_', $className);
        $name = "";
        foreach ($a as $k => $n) {
            if ($k > 0) {
                $name .= $n . '_';
            }
        }

        $name = substr($name, 0, - 1);

        return strtolower(Samus_CRUD::getTablePrefix() . $name);
    }

    /**
     * Constroi o prefixo do nome das colunas de uma tabela
     * @param string $tableName
     */
    public function buildAttrPrefix($tableName) {
        return substr($tableName, strlen(Samus_CRUD::getTablePrefix()));
    }

    /**
     * Deleta a tabela, usada durante o desenvolvimento caso seja
     * nescessário atualizar alguma coluna da tabela
     * @return resource mysqli_resource
     */
    private function dropTable() {
        $sql = "DROP TABLE IF EXISTS `" . Samus_Database_Connection::getBanco() . "`.`" . self::getDbTable() . "` ; \n";

        return Samus_CRUD::executeQuery($sql);
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

    protected function dropToCreate() {
        return $this->dropToCreate;
    }

    /**
     * Especifica se a tabela sera Dropada anes de criada
     * @param boolean $dropToCreate
     */
    protected function DropOriginalTableToCreate($dropToCreate) {
        $this->dropToCreate = $dropToCreate;
    }

    /**
     * Habilita os métodos de criação de tabelas, REDUZ O DESEMPENHo
     */
    public static function enableCreateTables() {
        self::$createTables = true;
    }

    /**
     * Disabilita os métodos de criação de tabelas, MELHORA O DESEMPENHo
     */
    public static function disableCreateTables() {
        self::$createTables = false;
    }

    /**
     * Verifica se o modo de criação de tabelas esta ativado
     * @return boolean
     */
    public static function isCreateTablesEnabled() {
        return self::$createTables;
    }

}

?>
