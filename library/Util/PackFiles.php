<?php

class Util_PackFiles {


    public function __construct() {

    }

    /**
     * Compacta todos os arquivos .php de um diret�rio removendo coment�rios,
     * espa�os e quebras de linhas desnecess�rias.
     * motivos de desempenho e seguran�a esse m�todo so interage em um diret�rio
     * de cada vez.
     *
     * @param string $directoryPath
     * @param string $newFilesDirectory
     * @param string $newFilesPrefix
     * @param string $newFilesSufix
     */
    public static function cleanDir($directoryPath , $newFilesDirectory="packed" , $newFilesPrefix="" , $newFilesSufix="") {

        $dir = new DirectoryIterator($directoryPath);

        mkdir($directoryPath . "/$newFilesDirectory/");

        while($dir->valid()) {

            if(!$dir->isDir() and !$dir->isDot() and substr($dir->getFilename(), -3, 3)=='php') {
                
                $str = self::cleanFile($dir->getPathname());
                
                $fp = fopen($dir->getPath() . "/packed/" . $newFilesPrefix . $dir->getFilename() . $newFilesSufix, "w");
                fwrite($fp, $str);
                fclose($fp);

                echo $dir->getPathname() . ' - Renomeado com sucesso <br />';
            }
            
            $dir->next();
        }

    }

    /**
     * Remove espa�os desnecess�rios e coment�rios de c�digos .php
     *
     * @param string $fileName camiho completo para um arquivo
     * @return string
     */
    public static function cleanFile($fileName) {
        return php_strip_whitespace($fileName);
    }

}

?>