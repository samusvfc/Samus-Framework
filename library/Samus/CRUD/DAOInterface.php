<?php
/**
 * Samus_CRUD_DAOInterface - Dynamic Acess Object
 *
 *
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version v 1.0.0
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category CRUD
 * @package CRUD
 */
interface Samus_CRUD_DAOInterface {

    /**
     * Obtem a chave primária unica do objeto
     */
    public function getId();

    /**
     * Especifica a chave primária do objeto
     * @param int $id
     */
    public function setId($id);

    /**
     * Obtem uma instancia do objeto gerado
     * @return object
     */
    public static function getInstance();

}

?>