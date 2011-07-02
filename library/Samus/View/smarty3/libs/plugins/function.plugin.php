<?php

function smarty_function_plugin($params, &$smarty) {

    $plugin = new Samus_PluginController($params['name'] , $params);
    return $plugin->displayPlugin();

}