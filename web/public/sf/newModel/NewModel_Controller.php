<?php

/**
 * Classe NewFilter_Controller
 *
 * @author Vinicius
 */
class NewModel_Controller extends Samus_Controller {

    public $files = array();
    public $dirOptions = array();
    public $errorMsg;
    public $error;
    public $sucesso = false;
    public $msg = '';
    public $url;
    public $phpDataTypes = array('boolean', 'integer', 'float', 'string', 'resource', 'mixed', 'number', 'int');
    public $classCode;

    public function index() {

        $c = parse_ini_file(Samus::GLOBAL_CONFIGURATION_FILE, false);

        if ($c['disable_sf_assistants'] == '1') {
            echo 'The "New Page" resource is disabled by "global_configuration.ini" options';
            exit();
        }

        $dir = new DirectoryIterator(WEB_DIR . Samus::MODELS_DIR);

        while ($dir->valid()) {
            if ($dir->isDir() && !$dir->isDot()) {
                $this->dirOptions[ucfirst($dir->getBasename())] = $dir->getBasename();
            }
            $dir->next();
        }
        $this->setRenderFile('');
    }

    public function createAction() {


        $className = $_POST['name'];

        $dirInName = explode('_', $className);
        $className = $dirInName[count($dirInName) - 1];
        $completeClassName = $_POST['name'];

        $dirInNameStr = '';



        for ($i = 0; $i < count($dirInName) - 1; $i++) {
            $dirInNameStr .= $dirInName[$i] . '/';
            $dirName = WEB_DIR . 'library/' . $dirInNameStr;

            if (!is_dir($dirName)) {
                if (mkdir($dirName, 0777)) {
                    $this->addMsg('The direcory "' . $dirName . '" was created');
                }
            }
        }

        $dirInNameStr = substr($dirInNameStr, 0, -1);
        $dir = WEB_DIR . 'library/' . $dirInNameStr;

        if (!is_dir($dir)) {
            $this->displayError('The selected directory doesn\'t exist ' . $dir);
        }


        $attrStr = '';
        $methodStr = '';

        foreach ($_POST['nome'] as $key => $n) {
            if (!empty($n)) {

                // $_POST['phpType'][$key] = strtolower($_POST['phpType'][$key]);
                $_POST['databaseType'][$key] = strtoupper($_POST['databaseType'][$key]);
                $_POST['databaseExtra'][$key] = strtoupper($_POST['databaseExtra'][$key]);

                $attrStr .= '
    /**
    * @var ' . $_POST['phpType'][$key] . ' ' . $_POST['databaseType'][$key] . ' ' . $_POST['databaseExtra'][$key] . '
    */
    private $' . $n . ';
';

                $methodStr .= '
    /**
     * @return ' . $_POST['phpType'][$key] . '
     */
    public function ' . Samus_CRUD_MethodSintaxe::buildGetterName($n) . '() {
        return $this->' . $n . ';
    }

    /**
     * @param ' . $_POST['phpType'][$key] . ' $' . $n . '
     */
    public function ' . Samus_CRUD_MethodSintaxe::buildSetterName($n) . '(' . $this->objectName($_POST['phpType'][$key]) . '$' . $n . ') {
        $this->' . $n . ' = $' . $n . ';
    }
';
            }
        }

        $classStr = '<?php
class ' . $completeClassName . ' extends Samus_Model {

' . $attrStr . '' . $methodStr . '

}

';
        $filename = $dir . '/' . $className . '.php';


        if (is_file($filename)) {
            $this->displayError('The file "' . $filename . '" already exist');
        } else {
            $r = fopen($filename, 'a');
            fwrite($r, $classStr);
            $this->addMsg('The class "' . $filename . '" was created');
            fclose($r);

            if (!chmod($filename, 0777)) {
                $this->addMsg('The permissions of ' . $filename . '" can`t be changed');
            }
        }
        if (isset($_POST['createTable']) && $_POST['createTable'] == 'true') {
            Samus::connect();
            require_once $filename;
            Samus_CRUD_TableFactory::enableCreateTables();
            $evalStr = 'new ' . $completeClassName . '();';
            eval($evalStr);
            Samus_CRUD_TableFactory::disableCreateTables();
            $this->addMsg(Samus_CRUD_TableFactory::$msg);
        }
    }

    /**
     * Verifica se éum tipo comum do PHP ou um objeto para fazer o correto setter
     * @param string $phpDataType
     * @return string
     */
    public function objectName($phpDataType, $addSpaceAfter=true) {
        if (in_array(strtolower($phpDataType), $this->phpDataTypes)) {
            return '';
        } else {
            if ($addSpaceAfter) {
                return $phpDataType . ' ';
            } else {
                return $phpDataType;
            }
        }
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