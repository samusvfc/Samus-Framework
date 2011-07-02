<?php

/**
 * Classe NewDir_Controller
 *
 * @author Vinicius
 */
class NewDir_Controller extends Samus_Controller {

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
            echo 'The "New Dir" resource is disabled by "global_configuration.ini" options';
            exit();
        }

        $dir = new DirectoryIterator(WEB_DIR . Samus::PUBLIC_DIR);

        $this->dirOptions[''] = '/';

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

        $baseDir = $_POST['directory'];
        $name = trim($_POST['name']);

        $cumulativeDir = $baseDir . '/' . $name;


        $arrayDir = explode('/', $cumulativeDir);

        $controller = $arrayDir[count($arrayDir) - 1];
        $directory = '';
        foreach ($arrayDir as $key => $a) {
            if ($key != count($arrayDir)) {
                $directory .= $a . '/';
            }
        }

        $dir = WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir;
        if (!is_dir($dir)) {
            mkdir($dir, 0777);
            $this->addMsg('The Base directory  "' . $dir . '" was created');
        } else {
            $this->displayError('The base directory "' . $dir . '" already exist');
        }


        if (isset($_POST['createCssDir']) && $_POST['createCssDir'] == 'true') {

            $dir = WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '/_css';
            if (!is_dir($dir)) {
                mkdir($dir, 0777);
                $this->addMsg('The CSS directory  "' . $dir . '" was created');
            } else {
                $this->displayError('Directory "' . $dir . '" already exist');
            }



//base css file
            $filename = WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '/_css/' . $name . '.css';
            if (!is_file($filename)) {
                $r = fopen($filename, 'a');
                fclose($r);
                
                
                if(!chmod($filename, 0777)) {
                    $this->addMsg('The permissions of ' . $filename . '" can`t be changed');
                }
                $this->addMsg('The CSS file  ' . $filename . '" was created');
            } else {
                $this->displayError('File "' . $filename . '" already exist');
            }

            $dir = WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '/_css/images/';
            if (!is_dir($dir)) {
                mkdir($dir, 0777);
                $this->addMsg('The Images directory was  ' . WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '/_css/images" created');
            } else {
                $this->displayError('Css directory "' . $dir . '" already exist');
            }
        }


        if (isset($_POST['createJsDir']) && $_POST['createJsDir'] == 'true') {
            $dir = WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '/_js';

            if (!is_dir($dir)) {
                mkdir($dir, 0777);
                $this->addMsg('The JS directory "' . WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '/_css/images" was created');
            } else {
                $this->displayError('JS directory "' . $dir . '" already exist');
            }

//base js file
            $filename = WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '/_js/' . $name . '.js';
            if (!is_file($filename)) {
                $r = fopen($filename, 'a');
                fclose($r);
                $this->addMsg('The JS file  ' . $filename . '" was created');
                
                if(!chmod($filename, 0777)) {
                    $this->addMsg('The permissions of ' . $filename . '" can`t be changed');
                }
                
                
            } else {
                $this->displayError('File "' . $filename . '" already exist');
            }
        }

        if (isset($_POST['createIncludeDir']) && $_POST['createIncludeDir'] == 'true') {
            $dir = WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '/_includes';

            if (!is_dir($dir)) {
                mkdir($dir, 0777);
                $this->addMsg('The Includes directory  "' . WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '/_css/images" was created');
            } else {
                $this->displayError('The Includes directory "' . $filename . '" already exist');
            }
        }


//base render file
        if (isset($_POST['createDefaultRenderFile']) && $_POST['createDefaultRenderFile'] == 'true') {

            $filename = WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '/' . $name . '.html';

            if (!is_file($filename)) {


                $html = '<?xml version="1.0" encoding="ISO-8859-1"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <title>${if $s->title}${$s->title} - ${/if} ' . $name . ' </title>
        ${$s->renderHead()}
        ${include file="sf/head.html"}
        <meta name="description" content="" />
        <meta name="keywords" content="" />
    </head>
    <body>
        <div id="root">

            <h1>' . $name . ' root</h1>

            ${$s->renderBody()}
            ${include file="sf/body.html"}
            ${$s->renderFooter()}
        </div>
    </body>
</html>';

                $r = fopen($filename, 'a');
                fwrite($r, $html);
                fclose($r);
                
                if(!chmod($filename, 0777)) {
                    $this->addMsg('The permissions of ' . $filename . '" can`t be changed');
                }

                $this->addMsg('The Default render file  ' . $filename . '" was created');
            } else {
                $this->displayError('The Default render file  ' . $filename . '" already exist');
            }
        }


        if (isset($_POST['createFilter']) && $_POST['createFilter'] == 'true') {
//filter
            $filterName = ucfirst($name) . Samus::FILTER_SUFIX;

            $filename = WEB_DIR . Samus::PUBLIC_DIR. $cumulativeDir . '/' . $filterName . '.php';

            if (!is_file($filename)) {

                $filterStr = "<?php
/**
 * Classe $filterName
 * @author samus
 */
class $filterName extends Samus_Filter {

    public function filter() {

    }

    public function endFilter() {

    }

}

";

                $r = fopen($filename, 'a');
                fwrite($r, $filterStr);
                fclose($r);
                
                if(!chmod($filename, 0777)) {
                    $this->addMsg('The permissions of ' . $filename . '" can`t be changed');
                }

                $this->addMsg('The Filter file  ' . $filename . '" was created');
            } else {
                $this->displayError('The Filter file  ' . $filename . '" already exist');
            }
        }


//index Controller
        if (isset($_POST['createIndex']) && $_POST['createIndex'] == 'true') {
            $dir = WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '/index/';
            $controllerName = 'Index_Controller';
            $filename = $dir . '/' . $controllerName . '.php';


            if (!is_dir($dir)) {
                mkdir($dir,0777);
                $this->addMsg('The Index Controller directory  ' . $dir . '" was created');
            } else {
                $this->displayError('The index controller directory "' . $dir . '" already exist');
            }


            if (!is_file($filename)) {


                $r = fopen($filename, 'a');

                $controllerStr = "<?php

class $controllerName extends Samus_Controller {

    public function index() {
        
    }

}
";
                fwrite($r, $controllerStr);
                fclose($r);
                
                if(!chmod($filename, 0777)) {
                    $this->addMsg('The permissions of ' . $filename . '" can`t be changed');
                }
                
                $this->addMsg('The Index Controller Class file  "' . $filename . '" was created');
            } else {
                $this->displayError('The Index Controller Class file  "' . $filename . '" already exist');
            }


            $filename = WEB_DIR . Samus::PUBLIC_DIR . $cumulativeDir . '/index/Index.html';
            if (!is_file($filename)) {
                fopen($filename, 'a');
                $this->addMsg('The Index view file  "' . $filename . '" was created');
                
                if(!chmod($filename, 0777)) {
                    $this->addMsg('The permissions of ' . $filename . '" can`t be changed');
                }
                
            } else {
                $this->displayError("The Index view file \"$filename\" already exist");
            }
        }

        $this->url = APP_URL.$baseDir.$name;
    }

    public function displayError($msg) {
        $this->error = true;
        $this->errorMsg .= $msg . '
';
    }

    public function addMsg($msg) {
        $this->msg .= '&raquo; '.$msg . ';
';
        $this->sucesso = true;
    }

}