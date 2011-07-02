<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Vinicius
 * Date: 07/04/11
 * Time: 18:13
 * To change this template use File | Settings | File Templates.
 */

abstract class Samus_Plugin extends Samus_Object {

    private $_pluginName;
    private $_className;
    private $_pluginDir;


    /**
     * Exibe o plugin
     * @return void
     */
    public function _displayPlugin() {

        $smarty = new Smarty();

        $this->getPluginName();
        $smarty->template_dir = WEB_DIR.'library/'. $this->_pluginDir;
        $smarty->compile_dir = WEB_DIR .'system/views_c/plugins';
        $smarty->cache_dir = WEB_DIR.'system/cache';
        $smarty->allow_php_tag = true;
        $smarty->left_delimiter = Samus::VIEWS_LEFT_DELIMITER;
        $smarty->right_delimiter = Samus::VIEWS_RIGHT_DELIMITER;


        //ref

        $ref = new ReflectionClass($this);
        $properties = $ref->getProperties();

        foreach($properties as $p) {
            /*@var $p ReflectionProperty*/
            $smarty->assign($p->getName() , $p->getValue($this));
        }

        $smarty->assign("dir" , APP_URL.'library/'. $this->_pluginDir.'/');


        $smarty->display( $this->getPluginName().".html");

    }

    public function addVar() {

    }

    /**
     * Obtem o nome do plugin e outros nomes
     * @return string
     */
    public function getPluginName() {
        if (empty($this->_pluginName)) {
            $ref = new ReflectionClass($this);
            $this->_className = $ref->getName();
            $a = explode('_', $ref->getName());
            $this->_pluginName = $a[count($a) - 1];

            $dir = "";
            foreach ($a as $k => $name) {
                if ($k < count($a)) {
                    $dir .= $name . '/';
                }
            }
            $dir = substr($dir, 0, -1);
            $this->_pluginDir = $dir;
        }

        return $this->_pluginName;
    }

    public function getPluginDir() {
        if (empty($this->_className)) {
            $this->getPluginName();
        }
    }

    public abstract function index();

}