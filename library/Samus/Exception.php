<?php
/**
 * Samus Exception
 *
 * Captura as exeções lançadas pelo CRUD PHP
 *
 *
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version v 1.0.0
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category samusframework
 * @package samusframework
 */
class Samus_Exception extends Exception {

   public function __construct($message, $code = 0) {
	  parent::__construct($message, $code);
   }

}