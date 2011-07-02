<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Vinicius
 * Date: 07/04/11
 * Time: 17:46
 * To change this template use File | Settings | File Templates.
 */
 
class Samus_PluginController  {

    private $name;
    private $vars = array();

    public function __construct($pluginName , $vars=array()) {

        $this->name = $pluginName;

        unset($vars['name']);
        
        $this->vars = $vars;
    }

    public function displayPlugin() {

        $evalStr = "";

        $p = null;
        $pluginName = "Samus_Plugin_".$this->name;

        eval('$p = new '.$pluginName.'();');

        foreach($this->vars as $k => $v) {
            eval('$p->'.$k.'=$v;');
        }
        eval('$p->index();');
        eval('$p->_displayPlugin();');
    }


}
