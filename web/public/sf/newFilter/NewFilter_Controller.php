<?php

/**
 * Classe NewFilter_Controller
 *
 * @author Vinicius
 */
class NewFilter_Controller extends Samus_Controller {

    public $files = array();
    public $dirOptions = array();
    public $errorMsg;
    public $error;
    public $sucesso = false;
    public $msg = '';
    public $url;

    public function index() {
        $c = parse_ini_file(Samus::GLOBAL_CONFIGURATION_FILE, false);


        if ($c['disable_sf_assistants'] == '1') {
            echo 'The "New Page" resource is disabled by "global_configuration.ini" options';
            exit();
        }

        $dir = new DirectoryIterator(WEB_DIR . Samus::PUBLIC_DIR);

        while ($dir->valid()) {
            if ($dir->isDir() && !$dir->isDot()) {
                if ($dir->getBasename() != 'sf')
                    $this->dirOptions[ucfirst($dir->getBasename())] = $dir->getBasename();
            }
            $dir->next();
        }
        $this->setRenderFile('');
    }

    public function createAction() {

        $name = str_replace(Samus::FILTER_SUFIX, '', trim($_POST['name']));
        $filterName = ucfirst($name . Samus::FILTER_SUFIX);

        $directory = $_POST['directory'];

        $arrayDir = explode('/', $directory);

        $controller = $arrayDir[count($arrayDir) - 1];
        $directory = '';
        foreach ($arrayDir as $key => $a) {
            if ($key != count($arrayDir) - 1) {
                $directory .= $a . '/';
            }
        }

        $filterStr = "<?php
/**
 * Classe Site_filter
 * @author samus
 */
class $filterName extends Samus_Filter {

    public function filter() {

    }

    public function endFilter() {
        
    }

}


";

        $cumulativeDir = '';
        foreach ($arrayDir as $a) {
            $cumulativeDir .= lcfirst($a . '/');
            if (!is_dir(WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir)) {
                mkdir(WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir, 0777);
                $this->msg .= 'Directory created: ' . WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '
';
            } else {
                $this->displayError('The directory "' . WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '" already exist');
            }
        }

        $filterName = WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . $filterName . '.php';
        ;

        if (file_exists($filterName)) {
            $this->displayError('The filter "' . $filterName . '" already exist');
            return false;
        }

        $r = fopen($filterName, 'w');
        fwrite($r, $filterStr);
        fclose($r);

        if (!chmod($filterName, 0777)) {
            $this->addMsg('The permissions of ' . $filename . '" can`t be changed');
        }

        $this->msg = "Filter \"" . $filterName . "\" created";
        $this->sucesso = true;
    }

    public function displayError($msg) {
        $this->error = true;
        $this->errorMsg .= $msg . '
';
    }

    public function addMsg($msg) {
        $this->msg .= '&raquo; ' . $msg . ';
';
        $this->sucesso = true;
    }

}