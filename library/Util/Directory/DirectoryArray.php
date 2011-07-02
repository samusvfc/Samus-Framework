<?php
require_once 'util/directory/Util_Datas_DirectoryArray.php';

/**
 * Description of Util_Datas_DirectoryArray
 *
 * @author samus
 */
class Util_Datas_DirectoryArray {

    private $dirArray = array();

    private $baseDir;
    
    private static $noIndexArray = array('.svn');
    
    public function __construct($baseDir) {
        $this->setBaseDir($baseDir);
    }
    
    /**
     * Adiciona um diretório para não ser indexado na analise, o nome desse 
     * diretório deve
     * @param $directoryName    
     */
    public static function addNoIndexDirectory($directoryName) {
        self::$noIndexArrray[] = $directoryName;
    }

    public function getDirArray() {

        $di = new DirectoryIterator($this->baseDir);

        while($di->valid()) {

            if(!$di->isDot() && $this->validateDir($di->getFilename()) ) {

                if($di->isDir()) {
                    $this->dirArray[$di->getFilename()] = $this->readDir($di->getPath() . '/' . $di->getFilename());
                } else {
                      $this->dirArray[$di->getFilename()] = $di->getPath() . "/" . $di->getFilename();
                }
            }

            $di->next();
        }

        return $this->dirArray;

    }
    
    /**
     * Valida se um atributo esta na lista dos noIndex
     * @param $fileName string
     * @return boolean
     */
    private function validateDir($fileName) {
        $retorno = true;
        foreach(self::$noIndexArray as $item) {
            if($fileName == $item) {
                $retorno = false;
                break;
            }
        }
        return $retorno;
    }
    
    public function readDir($dirPath) {
        $di = new DirectoryIterator($dirPath);

        $array = array();

        while($di->valid()) {

            if(!$di->isDot() && $this->validateDir($di->getFilename())) {
                if($di->isDir()) {

                    $dir = $di->getPath() . '/' . $di->getFilename();

                    $array[$di->getFilename()] = $this->readDir($di->getPath() . '/' . $di->getFilename());
                } else {
                    $array[$di->getFilename()] = $di->getPath() . "/" . $di->getFilename();
                }
            }
            
            $di->next();
        }

        return $array;
    }

    public function setDirArray($dirArray) {
        $this->dirArray = $dirArray;
    }

    public function getBaseDir() {
        return $this->baseDir;
    }

    public function setBaseDir($baseDir) {
        $this->baseDir = $baseDir;
    }




}
?>
