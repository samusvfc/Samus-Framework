<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Vinicius
 * Date: 07/04/11
 * Time: 21:22
 * To change this template use File | Settings | File Templates.
 */
 
class Samus_Plugin_ul extends Samus_Plugin {

    public $itens;

    public function index() {

        $this->itens = explode(',' , $this->itens);

    }

}
