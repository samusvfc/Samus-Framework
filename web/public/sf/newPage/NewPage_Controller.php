<?php

/**
 * Controlador NewPage_Controller
 *
 * @author Vinicius Fiorio - samus@samus.com.br
 */
class NewPage_Controller extends Samus_Controller {

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
                    $this->dirOptions[$dir->getBasename()] = $dir->getBasename();
            }
            $dir->next();
        }
        $this->setRenderFile('');
    }

    public function createAction() {

        $directory = $_POST['directory'];


        $arrayDir = explode('/', $directory . '/' . lcfirst(trim($_POST['name'])));

        $controller = $arrayDir[count($arrayDir) - 1];
        $directory = '';
        foreach ($arrayDir as $key => $a) {
            if ($key != count($arrayDir) - 1) {
                $directory .= $a . '/';
            }
        }

        $createHtml = false;
        $createCss = false;
        $createJs = false;
        $createHead = false;

        if (isset($_POST['createHtml']))
            $createHtml = true;

        if (isset($_POST['createCss']))
            $createCss = true;

        if (isset($_POST['createJs']))
            $createJs = true;

        if (isset($_POST['createHead']))
            $createHead = true;


        $controllerName = ucfirst($controller) . Samus::CONTROLS_CLASS_SUFIX;
        $baseDir = WEB_DIR . Samus::PUBLIC_DIR . $directory . $controller . '/';
        $controllerFileName = $baseDir . $controllerName;


        $this->url = APP_URL . $_POST['directory'] . '/' . lcfirst(trim($_POST['name']));

        if (is_file($controllerFileName . Samus::CONTROLS_FILES_EXTENSION)) {
            $this->displayError('O controlador ' . $directory . '/' . $controllerName . Samus::CONTROLS_FILES_EXTENSION . ' já existe');
        }



        $actionsStr = '';
        if (isset($_POST['actions']) && !empty($_POST['actions'])) {

            $actionsArray = explode('
', $_POST['actions']);

            foreach ($actionsArray as $key => $a) {
                $a = trim($a);

                $actionsStr .= "
    public function $a" . Samus::METHOD_URL_SUFIX . "() {
        
    }

";
            }
        }

        $renderStr = '';
        if ((isset($_POST['renderFile']) || !empty($_POST['renderFile'])) && $_POST['renderFile'] != "default") {
            $renderStr = '$this->setRenderFile("' . $_POST['renderFile'] . '")';
        }



        $templatesStr = '';
        if (isset($_POST['templates']) && !empty($_POST['templates'])) {

            $templatesArray = explode('
', $_POST['templates']);


            foreach ($templatesArray as $key => $a) {
                $a = trim($a);

                $templatesStr .= "
    public function $a" . Samus::METHOD_TEMPLATE_SUFIX . "() {

    }
";
            }
        }


        $controllerStr = "<?php

class $controllerName extends Samus_Controller {

    public function index() {
        $renderStr
    }

    $actionsStr $templatesStr
}
";
        $cumulativeDir = '';
        foreach ($arrayDir as $a) {
            $cumulativeDir .= $a . '/';

            if (!is_dir(WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir)) {
                mkdir(WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir, 0777);
                $this->msg .= 'Directory created: ' . WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '
';
            } else {
                $this->displayError('The directory "' . WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '" already exist');
            }
        }

        $r = fopen($controllerFileName . Samus::CONTROLS_FILES_EXTENSION, 'w');
        fwrite($r, $controllerStr);
        fclose($r);

        if (!chmod($controllerFileName . Samus::CONTROLS_FILES_EXTENSION, 0777)) {
            $this->addMsg('The permissions of ' . $controllerFileName . Samus::CONTROLS_FILES_EXTENSION . '" can`t be changed');
        }

        $this->msg .= 'Controller file created: ' . $controllerFileName . Samus::CONTROLS_FILES_EXTENSION . '
';

        if ($createHtml) {
            $filename = $baseDir . ucfirst($controller) . Samus::VIEWS_FILE_EXTENSION;
            if (!is_file($filename)) {
                $r = fopen($filename, 'a');
                $this->msg .= 'HTML view file created: ' . $filename . '
';
                fclose($r);

                if (!chmod($filename, 0777)) {
                    $this->addMsg('The permissions of ' . $filename . '" can`t be changed');
                }
            } else {
                $this->displayError('The file "' . $filename . '" already exist');
            }
        }
        if ($createHead) {
            $filename = $baseDir . ucfirst($controller) . '_Head' . Samus::VIEWS_FILE_EXTENSION;
            if (!is_file($filename)) {
                $r = fopen($filename, 'a');
                $this->msg .= 'HTML Head view file created: ' . $filename . '
';
                fclose($r);

                if (!chmod($filename, 0777)) {
                    $this->addMsg('The permissions of ' . $filename . '" can`t be changed');
                }
            } else {
                $this->displayError('The file "' . $filename . '" already exist');
            }
        }
        if ($createCss) {
            $filename = $baseDir . ucfirst($controller) . '.css';
            if (!is_file($filename)) {
                $r = fopen($filename, 'a');
                $this->msg .= 'CSS file created: ' . $baseDir . ucfirst($controller) . '.css' . '
';
                fclose($r);

                if (!chmod($filename, 0777)) {
                    $this->addMsg('The permissions of ' . $filename . '" can`t be changed');
                }
            } else {
                $this->displayError('The file "' . $filename . '" already exist');
            }
        }

        if ($createJs) {
            $filename = $baseDir . ucfirst($controller) . '.js';
            if (!is_file($filename)) {
                $r = fopen($filename, 'a');
                $this->msg .= 'JavaScript file created: ' . $filename . '.js' . '
';
                fclose($r);

                if (!chmod($filename, 0777)) {
                    $this->addMsg('The permissions of ' . $filename . '" can`t be changed');
                }
            } else {
                $this->displayError('The file "' . $filename . '" already exist');
            }
        }

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