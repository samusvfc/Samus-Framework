<?php

/**
 * Classe Samus_CRUD_DAOModel
 *
 * Através dessa classe é possível acessar os métodos da classe Samus_CRUD de forma
 * mais amigável e correta. As entidades devem extender a classe abstrata DAO
 * já que esta gerencia as instancias da Samus_CRUD_DAOModel e faz um acesso mais leve
 * aos métodos abaixo.
 *
 *
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version v 1.1.1
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category Samus_CRUD
 * @package Samus_CRUD
 */
class Samus_CRUD_DAOModel extends Samus_CRUD_Properties {

    /**
     * Instancia unica para Samus_CRUD
     * @var Samus_CRUD
     */
    private $crud = null;
    /**
     * Objeto analizado
     * @var Cidade
     */
    protected $object;
    /**
     * Nome da tabela
     * @var string
     */
    public static $dbTable;
    private $__conditions = array();
    private $__limits = "";
    private $__orders = array();
    private $__attributes = array();
    private $__extraAttributes = array(); // define os atributos extras que serão indexados separadamente
    private $__loadObjectsAttributes = true;
    private $groupBy;



    public function enableAutoQueryName() {
        Samus_CRUD::enableAutoQueryName();
    }

    /**
     * @var $queryName
     * @return Samus_CRUD_DAOModel
     */
    public function setQueryName($queryName) {
        $this->myCRUD()->setQueryName($queryName);
        return $this;
    }

    /**
     *
     * @return Samus_CRUD_DAOModel
     */
    public function forceUpdateQueryName() {
        Samus_CRUD::forceUpdateQueryName();
        return $this;
    }

    /**
     * Obtem o nome da tabela assocaida
     * @return string
     */
    public static function getDbTable() {
        return self::$dbTable;
    }

    /**
     * Construtor, passando o id carrega o elemento
     * @param int $id
     * @return void
     */
    public function __construct($id = "") {

        if (!empty($id)) {
            $this->load($id);
        }
    }

    /**
     * Carrega os atributos multivalorados
     * @param string $order
     * @param string $limit
     * @param boolean $loadInternalObjects se os objetos internos devem ser carregados
     * @return mixed
     * @deprecated
     */
    public function loadMultivaluedProperties($order = "", $limit = "", $loadInternalObjects = false) {
        if ($this->object->loadRule()) {

            $crud = $this->myCRUD();
            $crud->loadMultivaluedProperties($this->object, $order, $limit, $loadInternalObjects);
            return $this->object;
        } else {
            return false;
        }
    }

    /**
     * Adiciona Atributos que não serão carregados na indexação dos atributos das
     * classes. Muito util para a otimização de consultas e criação de objetos
     * mais leves
     *
     * @param $atribute1
     * @param $atribute2...
     * @return Samus_CRUD_DAOModel
     */
    public function removeAtributes($atribute1) {
        $crud = $this->myCRUD();
        foreach (func_get_args () as $arg) {
            $crud->addNoIndexAtribute($arg);
        }
        return $this;
    }

    /**
     * Faz a junção de outra classe alem da do objeto atual, ao unir clases diferentes
     * será possível realizar consultadas que levem em conta quaisquer atributos
     * de qualquer uma das classes.
     *
     * No sentido Classe 1 > Contem > Classe
     *
     * @param $className
     * @return Samus_CRUD_DAOModel
     */
    public function join($className, $attrName='') {
        $crud = $this->myCRUD();
        $crud->join($className, $attrName);
        return $this;
    }

    /**
     * Realiza um join preciso entre duas classes distintas, diferente dos outros 
     * joins esse join inicia uma nova junção entre tabelas ao invés de "navegar"
     * entre elas
     *
     * Caso os atributos não sejam especificados eles serão assumidos como a
     * coluna chave "Id"
     *
     * Os  Join são inseridos ao FINAL das querys de join
     *
     * @param string $className1 Nome da classe de origem da junção
     * @param string $className2 nome da classe alvo
     * @param string $attrName1 nome do atributo de junção da tabela 1
     * @param string $attrName2 nome do atributo de junção da tabela 2
     * @return Samus_CRUD_DAOModel
     */
    public function joinWith($className1, $className2, $attrName1="", $attrName2="", $className1Alias = "", $className2Alias="") {
        $crud = $this->myCRUD();
        $crud->joinWith($className1, $className2, $attrName1, $attrName2, $className1Alias, $className2Alias);
        return $this;
    }

    /**
     * Faz a junção de outra classe alem da do objeto atual, ao unir clases diferentes
     * será possível realizar consultadas que levem em conta quaisquer atributos
     * de qualquer uma das classes.
     *
     * No sentido Classe 1 << Esta Contida em << Classe 2
     *
     * @param $className
     * @return Samus_CRUD_DAOModel
     */
    public function joinReverse($className) {
        $crud = $this->myCRUD();
        $crud->reverseJoin($className);
        return $this;
    }

    /**
     * *
     * Carrega uma propriedade multivalorada do Objeto, este método resolve
     * relações N**1, para isso é preciso que na classe que carregará este
     * atributo multivalorado tenha um atributo do tipo array() com o mesmo
     * nome da classe multivalorada.
     *
     * Ex.:
     * Uma notícia possuí várias fotos
     *
     * class Noticia {
     * 	    [...]
     *
     * 	    private $foto = array();
     *
     * 	    [...]
     * }
     *
     * class Foto {}
     *
     * $noticia = new Noticia(1);
     * $noticia->getDao()->load_N_2_1_Propertie("foto);
     * var_dump($noticia->getFoto());
     *
     *
     * @param string $propertieName nome da propriedade
     * @param string $order ordem de carregamento dos elementos
     * @param string|int $limit
     * @param boolean $loadInternalObjectAtributes
     * @return mixed
     */
    public function load_N_2_1_Propertie($propertieName, $multivaloredClassName = "", $order = "", $limit = "", $loadInternalObjectAtributes = false) {
        if ($this->object->loadRule()) {
            $crud = $this->myCRUD();

            if (empty($order)) {
                $order = $this->getOrders();
            }

            if (empty($limit)) {
                $limit = $this->getLimits();
            }

            $crud->load_N_2_1_Propertie($this->object, $propertieName, $multivaloredClassName, $order, $limit, $loadInternalObjectAtributes);
            return $this->object;
        } else {
            return false;
        }
    }

    /**
     * Carrega os valores de  uma relação N_2_N entre 2 tabelas onde uma classe
     * asssociativa faz associação dos itens. Para carregar um atributo multivalorado
     * é preciso que ele seja uma propriedade
     *
     * A classe associativa "default" é a junção do nome das duas tabelas da
     * relação.
     * Ex.:
     * class Item {}
     * class Pedido {}
     *
     * Um item pode estar em vários Pedidos, e um pedido pode ter vários itens,
     * portanto é necessário uma classe associativa entre elas, sendo assim o
     * nome default dessa classe é ItemPedido
     *
     * class ItemPedido{}
     *
     * $pedido = new Pedido(1);
     * $pedido->getDao()->load_N_2_N_Propertie("item");
     * var_dump($pedido->getItem());
     *
     * @param string $propertieName
     * @return mixed
     */
    public function load_N_2_N_Propertie($propertieName, $associativeClassName = "", $order = "", $limit = "", $loadInternalObjectAtributes = true) {

        if ($this->object->loadRule()) {
            if (empty($order)) {
                $order = $this->getOrders();
            }

            if (empty($limit)) {
                $limit = $this->getLimits();
            }

            $crud->load_N_2_N_Propertie($this->object, $propertieName, $associativeClassName, $order, $limit, $loadInternalObjectAtributes);
            return $this->object;
        } else {
            return false;
        }
        $crud = $this->myCRUD();
    }

    /**
     * Seta os atributos que serão carregados nas operações
     *
     * @param string $atribute1
     * @param string $atribute2 ...
     * @return Samus_CRUD_DAOModel
     */
    public function setAtributes($atribute1) {
        $atributes = func_get_args();
        $this->myCRUD()->setAtributes($atributes);
        return $this;
    }

    /**
     * Limpa os atributos caso tenham sido especificados
     * @return Samus_CRUD_DAOModel
     */
    public function clearAtributes() {
        $this->myCRUD()->clearAtributes();
        return $this;
    }

    /**
     * Retorna um node XML do objeto, as tags HTML dos conteudos dos obetos são
     * codigicados por htmlentities()
     *
     * @param string $whereCondition condição de carregamento
     * @param string $order ordem dos registros
     * @param string $limit limite de registros
     * @param boolean $lightMode se serão carregados os objetos dentro dos objetos
     * @param boolean $addXmlRootTags
     * @return string node xml dos registros
     */
    public function loadXml($whereCondition = "", $order = "", $limit = "", $lightMode = false, $addXmlRootTags = false, $groupBy="") {



        if (empty($whereCondition)) {
            $whereCondition .= $this->getConditions();
        }

        if (empty($order)) {
            $order = $this->getOrders();
        }

        if (empty($limit)) {
            $limit = $this->getLimits();
        }

        if (empty($groupBy)) {
            $groupBy = $this->groupBy;
        }

        if ($this->__loadObjectsAttributes == false) {
            $lightMode = true;
        }

        $crud = $this->myCRUD();

        $crud->loadLightArray($whereCondition, $order, $limit, false, !$lightMode, true, $groupBy);

        $str = Samus_CRUD::$xmlStr;

        if ($addXmlRootTags) {
            $str = '<?xml version="1.0" encoding="ISO-8859-1"?>
<root>' . $str . "</root>";
        }

        Samus_CRUD::$xmlStr = "";

        return $str;
    }

    /**
     * Salva o objeto em $filename no formato xml
     *
     * @param string $filename
     * @param string|int $whereCondition
     * @param string $order
     * @param string|int $limit
     * @param boolean $lightMode
     * @param string $fopenMode w - zera o arquivo _ a - continua de onde parou
     * @return boolean caso o arquivo seja criado
     */
    public function saveXml($filename, $whereCondition = "", $order = "", $limit = "", $lightMode = false, $fopenMode = "w") {
        if ($this->object->saveRule()) {



            if (empty($whereCondition)) {
                $whereCondition .= $this->getConditions();
            }

            if (empty($order)) {
                $order = $this->getOrders();
            }

            if (empty($limit)) {
                $limit = $this->getLimits();
            }

            if (!$handle = fopen($filename, $fopenMode)) {
                throw new Exception("O arquivo não pode ser aberto");
            }

            if (is_writable($filename)) {

                $strXml = $this->loadXml($whereCondition, $order, $limit, $lightMode, TRUE);

                if (fwrite($handle, $strXml) === FALSE) {
                    throw new Exception("Não foi possível escrever no arquivo ($filename)");
                }

                fclose($handle);

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Carrega o objeto a partir do seu id ou de uma condição, o funcionamento é
     * semelhanta ao WHERE de uma consulta MySql, caso whereCondition seja um
     * inteiro o objeto será carregado pelo ID da linha da tabela que representa
     * a entidade, se a string for menor do que 4 ela será automaticamente
     * convertido para int <br />
     * <br />
     * Ex.: <br />
     * $produto = new Produto(); <br />
     * $produto->load($id); <br />
     * var_dump($produto); <br />
     * <br />
     * <br />
     * Ex. 2: <br />
     * $usuario = new Usuario(); <br />
     * $usuario->load("email=$email AND senha=$senha"); <br />
     * var_dump($usuario);
     * @param int|string $id
     */
    public function load($whereCondition="", $loadArrayAtributes = false) {

        if ($this->object->loadRule()) {

            if (empty($whereCondition)) {
                $whereCondition .= $this->getConditions();
            }

            if (is_numeric($whereCondition)) {
                $whereCondition = (int) $whereCondition;
            }

            $crud = $this->myCRUD();

            $this->object = $crud->mountObject($whereCondition, "", $this->object);

            if ($loadArrayAtributes) {
                $crud->loadArrayAttributes($this->object);
            }

            return $this->object;
        } else {
            return false;
        }
    }

    /**
     * Encontra um objeto por uma de suas propriedades
     *
     * @param string $property
     * @param string $value
     * @return object
     * @deprecated
     */
    public function findBy($property, $value) {
        if ($this->object->loadRule()) {
            return $this->load("$property='$value'");
        } else {
            return false;
        }
    }

    /**
     * Carrega as proprieidades de um objeto a partir de um array associativo,
     * apenas as propriedades especificadas serão carregadas.: <br />
     * <br />
     * Ex.:
     * $array = array( "nome" => "Vnicius Fiorio" , "email" => "Samusdev@gmail.com");
     * $pessoa = new Pessoa();
     * $pessoa->loadObjectFromAssociativeArray($array);
     *
     * @param array $associativeArray
     * @return Samus_CRUD_DAOModel
     */
    public function loadObjectFromAssociativeArray(array $associativeArray, $loadObjectAtributes = false) {
        if ($this->object->loadRule()) {

            $crud = $this->myCRUD();
            $crud->mountAssociativeObject($associativeArray, $this->object, $loadObjectAtributes);
            return $this;
        } else {

            return false;
        }
    }

    /**
     * Lista os objetos da classe colocando em um vetor os objetos <br />
     * Ex.: <br />
     * $produto = new Produto(); <br />
     * $produtos_array = $produto->loadArrayList("categoria = 1"); <br />
     * var_dump($produtos_array); <br />
     * <br />
     *
     * @param string $whereCondition
     * @param string $order
     * @param string|int $limit
     * @return array
     */
    public function loadArrayList($whereCondition = "", $order = "", $limit = "", $groupBy="") {
        if ($this->object->loadRule()) {
            if (empty($whereCondition)) {
                $whereCondition .= $this->getConditions();
            }

            if (empty($order)) {
                $order = $this->getOrders();
            }

            if (empty($limit)) {
                $limit = $this->getLimits();
            }

            if (empty($groupBy)) {
                $groupBy = $this->groupBy;
            }


            $crud = $this->myCRUD();
            return $crud->loadLightArray($whereCondition, $order, $limit, false, $this->__loadObjectsAttributes, false, $groupBy);
        } else {
            return false;
        }
    }

    /**
     * Analise a classe e as associaçoes e retorna um array associativo com os
     * resultados, é a consulta mais rápida dos dados mas não retorna Objetos,
     * ótimo para listagens que é essencial o desempenho
     *
     * @param string $whereCondition
     * @param string $order
     * @param string|int $limit
     * @return array array associativo
     */
    public function loadAssociativeArrayList($whereCondition = "", $order = "", $limit = "", $groupBy="") {
        if ($this->object->loadRule()) {



            if (empty($whereCondition)) {
                $whereCondition .= $this->getConditions();
            }

            if (empty($order)) {
                $order = $this->getOrders();
            }

            if (empty($limit)) {
                $limit = $this->getLimits();
            }

            if (empty($groupBy)) {
                $groupBy = $this->groupBy;
            }

            $crud = $this->myCRUD();
            return $crud->loadLightArray($whereCondition, $order, $limit, true, $this->__loadObjectsAttributes, false, $groupBy);
        } else {
            return false;
        }
    }

    /**
     * Alias para loadAssociativeArrayList - carrega uma matriz de arrays
     * associativas dos objetos
     *
     * @see function loadAssociativeArrayList
     * @param string $whereCondition
     * @param string $order
     * @param string|int $limit
     * @return array array associativo
     */
    public function find($whereCondition = "", $order = "", $limit = "", $associativeArray = true, $loadObjectAtributes = true, $groupBy="") {

        if ($this->object->loadRule()) {



            if (empty($whereCondition)) {
                $whereCondition .= $this->getConditions();
            }

            if (empty($order)) {
                $order = $this->getOrders();
            }

            if (empty($limit)) {
                $limit = $this->getLimits();
            }

            if (empty($groupBy)) {
                $groupBy = $this->groupBy;
            }


            if ($this->__loadObjectsAttributes == false) {
                $loadObjectAtributes = $this->__loadObjectsAttributes;
            }

            $crud = $this->myCRUD();
            return $crud->loadLightArray($whereCondition, $order, $limit, $associativeArray, $loadObjectAtributes, false, $groupBy);
        } else {
            return false;
        }
    }

    /**
     * Carrega um unico array associativo com os valores do objeto, sendo os parametros
     * suas chaves
     * @param $whereCondition
     * @param $order
     * @param $loadObjectAtributes
     */
    public function loadAssociative($whereCondition, $loadObjectAtributes = false, $order = 'id DESC') {
        if ($this->object->loadRule()) {



            if (empty($whereCondition)) {
                $whereCondition .= $this->getConditions();
            }

            if (empty($order)) {
                $order = $this->getOrders();
            }

            $crud = $this->myCRUD();
            return $crud->loadAssociative($whereCondition, $loadObjectAtributes, $order);
        } else {
            return false;
        }
    }

    /**
     * Carrega um array associativo da classe
     * @param string $sql
     * @return array associativo
     */
    public function findFromSql($sql) {
        if ($this->object->loadRule()) {



            $crud = $this->myCRUD();
            return $crud->findFromSql($sql);
        } else {
            return false;
        }
    }

    /**
     * Carreta os atributos do tipo array do objeto, são as ligações 0..*
     *
     * classe tem com suas associa??es
     */
    public function loadObjectArrayAttributes($order = null, $limit = null) {
        if ($this->object->loadRule()) {
            $crud = $this->myCRUD();
            $crud->loadMultivaluedProperties($this->object, $order, $limit);
        } else {
            return false;
        }
    }

    /**
     * Carrega uma lista de objetos a partir de uma sql qualquer, o caracter '?'
     * será substituido pelo nome da tabela corrente
     *
     * @param string $sql
     * @param $loadObjectAtributes se os atrivutos do tipo objeto serão carregados
     * @return array de objetos
     */
    public function loadArrayObjectsFromSql($sql, $loadObjectAtributes = true) {
        if ($this->object->loadRule()) {
            $crud = $this->myCRUD();
            return $crud->loadArrayFromSql($sql, $loadObjectAtributes);
        } else {
            return false;
        }
    }

    /**
     * Carrega o ultimo objeto registrado no banco da entidade <br />
     * <br />
     * Ex.: <Br />
     * $produto = new Produto(); <br />
     * $produto->loadLast();
     * @param string $whereCondition
     * @return Samus_CRUD_DAOModel
     */
    public function loadLast($whereCondition = "") {
        if ($this->object->loadRule()) {



            if (empty($whereCondition)) {
                $whereCondition .= $this->getConditions();
            }


            $crud = $this->myCRUD();
            $crud->loadLastObject($whereCondition, $this->object);
            return $this;
        } else {
            return false;
        }
    }

    /**
     * Carrega o primeiro objeto registrado na tabela da entidade que atendem
     * a condição especificada
     * <br />
     * Ex.: <Br />
     * $produto = new Produto(); <br />
     * $produto->loadFirst();
     * @param string $whereCondition
     * @return Samus_CRUD_DAOModel
     */
    public function loadFirst($whereCondition = "") {
        if ($this->object->loadRule()) {



            if (empty($whereCondition)) {
                $whereCondition .= $this->getConditions();
            }

            $crud = $this->myCRUD();
            $crud->loadLastObject($whereCondition, $this->object, true);
            return $this;
        } else {
            return false;
        }
    }

    /**
     * Salva o objeto no banco. Caso o ID do objeto ja exita no banco o
     * mesmo será atualizado (UPDATE) senão ele será inserido (INSERT) <br />
     * Ex.: <br />
     * $produto = new Produto();<br />
     * $produto->setDescricao("Descrião do produto");<br />
     * $produto->setNome("Nome");<br />
     * $produto->setPreco(45,5);<br />
     * $produto->save();<br />
     * @return int|null id do objeto inserido, caso insert
     */
    public function save($validBefore = false) {
        if ($this->object->saveRule()) {
            $crud = $this->myCRUD();

            if ($validBefore) {
                if ($this->valid(false)) {
                    return $crud->save($this->object);
                } else {
                    return false;
                }
            } else {
                return $crud->save($this->object);
            }
        } else {
            return false;
        }
    }

    /**
     * Força que atributos não especificados ou vazios no Objeto sejam tratados
     * como vazios e não ignorados
     * @return Samus_CRUD_DAOModel
     */
    public function forceNull() {
        $this->myCRUD()->forceNull();
        return $this;
    }

    /**
     * Salva um array de objetos, todos os objetos do array devem ser intances
     * da mesma classe
     *
     * @param array $objectArray
     */
    public function saveObjectArray(array $objectArray) {
        $crud = $this->myCRUD();
        foreach ($objectArray as $obj) {
            if (is_a($obj, get_class($this->object))) {
                $obj->getDao()->save();
            }
        }
    }

    /**
     * Deleta o objeto especificado <br />
     * Ex.: <br />
     * $produto = new Produto(); <br />
     * $produto->setId(1); <br />
     * $produto->delete(); <br />
     *
     * @param mixed $object
     * @param string $whereCondition
     * @return boolean
     */
    public function delete($whereCondition = "") {
        if ($this->object->deleteRule()) {
            if (empty($whereCondition)) {
                $c = $this->getConditions();

                if (empty($c)) {
                    $whereCondition .= $c;
                } else {
                    $whereCondition = (int) $this->object->getId();
                }
            }



            $crud = $this->myCRUD();
            return $crud->delete($this->object, $whereCondition);
        } else {
            return false;
        }
    }

    /**
     * Deleta uma lista de objetos, todos os objetos deletados devem obedecer a
     * condição especificada
     *
     * @param object $objectArray
     * @param string $whereCondition
     */
    public function deleteObjectArray($objectArray, $whereCondition = "") {

        if ($this->object->deleteRule()) {
            $crud = $this->myCRUD();
            foreach ($objectArray as $obj) {
                if (is_a($obj, get_class($this->object))) {
                    $obj->getDao()->delete($whereCondition);
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Lista todos os elementos a partir dos termos da busca, ele varre a tabela
     * do banco procurando em qualquer coluna da tabela pelo elemento especificado
     * por 'search'. <br />
     * <br />
     * Ex.: <br />
     * $produto = new Produto(); <br />
     * $martelos = $produto->search("martelo"); <br />
     * var_dump($martelos); <br />
     * <br />
     *
     *
     * @param string $search
     * @param string$whereCondtion
     * @param string $order
     * @param string|int $limit
     * @param boolean $exactKeyword
     * @param $light se é feita uma consulta leve
     * @return mixed[]
     */
    public function search($search, $whereCondition = '', $order = '', $limit = '', $exactKeyword = false, $returnAssociativeArray = false, $loadObjectAttributes = true, $groupBy="") {
        if ($this->object->loadRule()) {

            if (empty($whereCondition)) {
                $whereCondition = $this->getConditions();
            }

            if (empty($order)) {
                $order = $this->getOrders();
            }

            if (empty($limit)) {
                $limit = $this->getLimits();
            }

            if (empty($groupBy)) {
                $groupBy = $this->groupBy;
            }

            $exact = "";
            if (!$exactKeyword)
                $exact = "%";

            $crud = $this->myCRUD();

            $atrArray = $crud->getAtributes();

            $query = "(";

            $ai = new ArrayIterator($atrArray);
            while ($ai->valid()) {
                $query .= " " . $crud->getTableName() . "." . $ai->current() . " LIKE '$exact$search$exact'" . " OR ";
                $ai->next();
            }
            $query = substr($query, 0, - 3);
            $query .= ")";

            if (!empty($whereCondition)) {
                $whereCondition = trim($whereCondition);
                if (substr($whereCondition, 0, 3) != "AND" && substr($whereCondition, 0, 2) != "OR") {
                    $whereCondition = "AND " . $whereCondition;
                }
                $query = $query . $whereCondition;
            }

            if ($returnAssociativeArray) {

                return $crud->loadLightArray($query, $order, $limit, true, $loadObjectAttributes, false, $groupBy);
            } else {
                if ($loadObjectAttributes == false) {
                    $this->disableLoadObjectAttributes();
                }
                return $this->loadArrayList($query, $order, $limit, $groupBy);
            }
        } else {
            return false;
        }
    }

    /**
     * Retorna um array associativo do objeto: <br />
     * $array["propriedade"] = "valor";
     * @return array
     */
    public function associativeArray() {

        if ($this->object->loadRule()) {
            $crud = $this->myCRUD();
            return $crud->associativeArrayFromEncapsuledObject($this->object);
        } else {
            return false;
        }
    }

    /**
     * Retorna um array associativo do objeto: <br />
     * $array["propriedade"] = "valor";
     * @return array
     */
    public function toArray() {
        return $this->associativeArray();
    }

    /**
     * @return Samus_CRUD
     */
    public function myCRUD() {
        if ($this->crud == null) {
            $ref = new ReflectionClass($this->object);
            $this->crud = Samus_CRUD_Singleton::getInstance("Samus_CRUD", $ref->getName(), $ref->getName());
            //$this->crud = new Samus_CRUD($ref->getName());
            $this->crud->addNoIndexAtribute("dao");
        }
        return $this->crud;
    }

    /**
     * @param Samus_CRUD $crud
     */
    public function setCrud($crud) {
        $this->crud = $crud;
    }

    /**
     * Obtem a instancia do Samus_CRUD do objeto
     * @return Samus_CRUD
     */
    public function getCRUD() {
        return $this->crud;
    }

    /**
     * Especifica o objeto usado nas operações
     * @param $object
     */
    public function setObject($object) {
        $this->object = $object;
    }

    /**
     * Retorna o ID da classe,
     * @return string
     */
    public function __tostring() {
        return (string) $this->object->getId();
    }

    /**
     * Valida o objeto utilizando Samus_CRUD_RequestValidator
     * @param $showErrorMessages boolean define se a
     * @return boolean
     * @deprecated
     */
    public function valid($showErrorMessages = false) {
        require_once 'Samus/Samus_CRUD/Samus_CRUD_RequestValidator.php';
        $req = new Samus_CRUD_RequestValidator($this->object);

        if ($showErrorMessages) {
            $req->init();
            echo $req->result();
            return $req->valid();
        } else {
            return $req->valid();
        }
    }

    /**
     * Valida o objeto retornando a mensagem de erro
     * @return string
     * @deprecated
     */
    public function validAndGetErrorMsg() {
        require_once 'Samus/Samus_CRUD/Samus_CRUD_RequestValidator.php';
        $req = new Samus_CRUD_RequestValidator($this->object);
        $req->init();
        return $req->result();
    }

    /**
     * Obtem o nome da classe modelo base
     * @return string nome da classe modelo base
     */
    public function getClassName() {
        return $this->myCRUD()->getClassName();
    }

    /**
     * Trata o array de condições e devolve ele em uma string valida para ser
     * avaliada pelos metodos de condições
     * @return string
     */
    private function getConditions() {
        $str = "";
        if (!empty($this->__conditions)) {
            foreach ($this->__conditions as $c) {
                $str .= $c . " ";
            }
        }

        return $str;
    }

    /**
     * Pega os atributos adicionados e adiciona ao Samus_CRUD
     */
    private function addAtrributesToCRUD() {
        if (!empty($this->__attributes)) {
            $this->myCRUD()->setAtributes($this->__attributes);
        }
    }

    /**
     * Obtem o limit especificado, como o limit é um atributo unico ele não precisa
     * ser tratado como array
     * @return string
     */
    private function getLimits() {
        return $this->__limits;
    }

    /**
     * Obtem as ordens
     * @return string
     */
    private function getOrders() {
        $str = "";
        if (!empty($this->__orders)) {
            foreach ($this->__orders as $c) {
                $str .= $c . " , ";
            }

            $str = substr($str, 0, - 2);
        }
        return $str;
    }

    /**
     *
     * Adiciona uma condição de carregamento à ser tratada como OR caso aja
     * alguma condição anteriormente adicionada à ela, a condição de
     * carregamento será adicionada no sentido de adição
     * Adiciona uma condição de carregamento
     *
     *  Ex.:
     *  $cidade = new Cidade();
     *  $cidade->getDao()->addAnd("nome='Vitoria')->addAnd('estado=8')->load();
     *
     * @param $condition string
     * @return Samus_CRUD_DAOModel
     */
    public function andCondition($condition) {

        if (empty($this->__conditions)) {
            $this->__conditions [] = $condition;
        } else {
            $this->__conditions [] = " AND " . $condition;
        }

        return $this;
    }

    /**
     * Alias para andCondition
     * @see andCondition
     * @return Samus_CRUD_DAOModel
     */
    public function andWhere($condition) {
        return $this->andCondition($condition);
    }

    /**
     * Adiciona uma condição de carregamento à ser tratada como OR caso aja
     * alguma condição anteriormente adicionada à ela, a condição de
     * carregamento será adicionada no sentido de adição
     *
     *  Ex.:
     *  $cidade = new Cidade();
     *  $cidade->getDao()->addOr("nome='Vitoria')->addOr('estado=8')->load();
     *
     * @param $condition string
     * @return Samus_CRUD_DAOModel
     */
    public function orCondition($condition) {
        if (empty($this->__conditions)) {
            $this->__conditions [] = $condition;
        } else {
            $this->__conditions [] = " OR " . $condition;
        }
        return $this;
    }

    /**
     * Adiciona uma condição generica da forma tradicional, OR e ANDS devem ser
     * adicionados para condições multiplas
     *
     *  Ex.:
     *  $cidade = new Cidade();
     *  $cidade->getDao()->addCondition("nome='Vitoria')->addOr('estado=8')->load();
     *
     * @return Samus_CRUD_DAOModel
     */
    public function where($condition, $logicalLink = "and") {

        if (!empty($this->__conditions)) {

            $cleanCondtion = trim(strtolower($condition));

            if (substr($cleanCondtion, 0, 3) != "and" && substr($cleanCondtion, 0, 2) != 'or') {
                $condition = " $logicalLink " . $condition;
            }
        }

        $this->__conditions[] = $condition;
        return $this;
    }

    /**
     * Adiciona um atributo para ser carregado, este metodo é opcionald, deve ser
     * usado apenas quando é necesário carregar especificamente um ou outro atributo
     * @return Samus_CRUD_DAOModel
     */
    public function addAttribute($attribute) {

        $atributeArray = explode(',', $attribute);
        foreach ($atributeArray as $a) {
            $this->__attributes[] = trim($a);
        }

        $this->addAtrributesToCRUD();

        return $this;
    }

    /**
     * Adiciona um atributo extra p/ ser carregado nas querys de multiplas tabelas
     *
     * - Os valores extras são recuperados pelos meetodos getExtra() e getExtraValue()
     * - Para arrays associativos, eles são estruturados dentro de array aninhados
     * - Não funciona para XML
     * - Não são carregados objetos internos das classes associadas
     * 
     * @param string $className
     * @param string $attribute
     * @return Samus_CRUD_DAOModel
     */
    public function addExtraAttribute($className, $attribute) {
        $this->myCRUD()->setExtraAtributes($className, $attribute);
        return $this;
    }

    /**
     * Alias para AddAttribute, adiciona um atributo caso seja necessário especificar
     * exatamente o que deve ser carregado
     * @param $atribute
     * @return Samus_CRUD_DAOModel
     */
    public function addAttr($atribute) {
        $this->addAttribute($atribute);
        return $this;
    }

    /**
     * Adiciona um limit de itens para serem carregados, faz sentido apenas em
     * contextos onde o serão carregados mais de um item
     * @param $initialOrTotal especifica o valor inicial ou total de itens que deve ser carregados
     * @param $final espcifica a posição final dos itens Ex.: 10,20 carrega os itens entre 10 e 20
     * @return Samus_CRUD_DAOModel
     */
    public function limit($initialOrTotal, $final = "") {
        $limit = $initialOrTotal;

        if (!empty($final)) {
            $limit .= ",$final";
        }

        $this->__limits = $limit;
        return $this;
    }

    /**
     * Adiciona um limit no formato de distancia, onde o valor final do LIMIT é
     * dado pela soma do initial + length
     * @param $initial posição inicial
     * @param $length numero de itens que serão carregados
     * @return Samus_CRUD_DAOModel
     */
    public function range($initial, $length) {
        $this->__limits = $initial . ',' . ($initial + $length);
        return $this;
    }

    /**
     * Adiciona um item para ser tratado como ordem de carregamento
     * @param $orderAttribute atributo que definira a ordem
     * @param $ascOrDesc se será ASC Ascendente ou DESC descendente
     * @return Samus_CRUD_DAOModel
     */
    public function orderBy($orderAttribute, $ascOrDesc = "") {
        if ($ascOrDesc)
            $this->__orders [] = $orderAttribute . ' ' . $ascOrDesc;
        else
            $this->__orders [] = $orderAttribute;

        return $this;
    }

    /**
     * Alias para Join, adiciona uma classe para ser carregada
     * @see Samus_CRUD_DAOModel::join
     * @return Samus_CRUD_DAOModel
     */
    public function addClass($className) {
        $this->join($className);
        return $this;
    }

    /**
     * Aplica a função group by ao resultado
     *
     * @param string $attributeName nome do atributo que será agrupado
     * @return Samus_CRUD_DAOModel
     */
    public function groupBy($attributeName) {
        $this->groupBy = $attributeName;
        return $this;
    }

    /**
     * Habilita a clausula distinct
     * @return Samus_CRUD_DAOModel
     */
    public function enableDistinct() {
        $this->myCRUD()->enableDistinct();
        return $this;
    }

    /**
     * Desabilita a clausula, distinct das querys
     * @return Samus_CRUD_DAOModel
     */
    public function disableDistinct() {
        $this->myCRUD()->disableDistinct();
        return $this;
    }

    /**
     * Retorna o numero de resultados armazenados na condição especificada
     *
     * @return int
     */
    public function count($whereCondition="", $groupBy="", $distinctWhat="*") {

        if (empty($whereCondition)) {
            $whereCondition = $this->getConditions();
        }

        if (empty($groupBy)) {
            $groupBy = $this->groupBy;
        }

        return $this->myCRUD()->count($whereCondition, $groupBy, $distinctWhat);
    }

    /**
     * Força o não carregamento de atributos do tipo objeto
     * @return Samus_CRUD_DAOModel
     */
    public function disableLoadObjectAttributes() {
        $this->__loadObjectsAttributes = false;
        return $this;
    }

    /**
     * Limpa todas as condições estabelecidas no objeto
     * @return Samus_CRUD_DAOModel
     */
    public function clearCondtions() {
        $this->__conditions = array();
        return $this;
    }

    /**
     * Limpa todos os limits estabelecidos
     * @return Samus_CRUD_DAOModel
     */
    public function clearLimits() {
        $this->__limits = "";
        return $this;
    }

    /**
     * Limpa todas as ordens adicionadas ao objeto
     * @return Samus_CRUD_DAOModel
     */
    public function clearOrders() {
        $this->__orders = array();
        return $this;
    }

    /**
     * Limpa todos os GroupBy
     * @return Samus_CRUD_DAOModel
     */
    public function clearGroupBy() {
        $this->groupBy = "";
        return $this;
    }

    /**
     * Limpa todas as especificações feitas para o objeto aplicando
     * clearAtributes()
     * clearConditions()
     * clearLimits()
     * clearGroupBy()
     * clearOrders()
     *
     * @return Samus_CRUD_DAOModel
     */
    public function clearAll() {
        $this->clearAtributes();
        $this->clearCondtions();
        $this->clearLimits();
        $this->clearGroupBy();
        $this->clearOrders();
        $crud = $this->myCRUD();
        $crud->joinClear();
        $crud->clearExtraAttributes();
        return $this;
    }


    /**
     * Completa o objeto conforme os atributos informados
     */
    public function autoComplete($aditionalConditions="") {
        if ($this->object->loadRule()) {

            if (empty($aditionalConditions)) {
                $aditionalConditions .= $this->getConditions();
            }

            $this->myCRUD()->autoComplete($this->object);
            return $this;
        } else {
            return false;
        }
    }


    /**
     * Encontra uma lista de arrays associativos usando como condição os atributos
     * preenchidos do objeto de origem
     *
     * $test = new Model_Test();
     * $test->setName("Samus");
     * $array = $test->loadArrayListFromObject();
     *
     * retorna todos os objetos com nome igual à samus
     *
     * @param string $aditionalConditions
     * @param string $order
     * @param string $limit
     * @param string $groupBy
     * @param boolean $returnAssociativeArray
     * @return array
     */
    public function loadArrayListFromObject($aditionalConditions="", $order = "", $limit = '', $groupBy="", $returnAssociativeArray=false) {
        if ($this->object->loadRule()) {
            if (empty($aditionalConditions)) {
                $aditionalConditions .= $this->getConditions();
            }

            if (empty($order)) {
                $order = $this->getOrders();
            }

            if (empty($limit)) {
                $limit = $this->getLimits();
            }

            if (empty($groupBy)) {
                $groupBy = $this->groupBy;
            }

            $crud = $this->myCRUD();
            //($obj, $order, $limit, $returnAssociativeArray, $loadObjectAtributes, $groupBy)
            return $crud->loadArrayFromObject($this->object, $aditionalConditions, $order, $limit, $returnAssociativeArray, $this->__loadObjectsAttributes, $groupBy);
        } else {
            return false;
        }
    }

    /**
     * Encontra uma lista de arrays associativos usando como condição os atributos
     * preenchidos do objeto de origem
     *
     * $test = new Model_Test();
     * $test->setName("Samus");
     * $array = $test->findFromObject();
     *
     * retorna todos os objetos com nome igual à samus
     *
     * @param string $aditionalConditions
     * @param string $order
     * @param string $limit
     * @param string $groupBy
     * @param boolean $returnAssociativeArray
     * @return array
     */
    public function findFromObject($aditionalConditions="", $order = "", $limit = '', $groupBy="", $returnAssociativeArray=true) {
        return $this->loadArrayListFromObject($aditionalConditions, $order, $limit, $groupBy, $returnAssociativeArray);
    }

}