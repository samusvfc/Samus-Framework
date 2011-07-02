<?php

class Util_PackFiles {


    public function __construct() {

    }

    /**
     * Compacta todos os arquivos .php de um diretório removendo comentários,
     * espaços e quebras de linhas desnecessárias.
     * motivos de desempenho e segurança esse método so interage em um diretório
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
     * Remove espaços desnecessários e comentários de códigos .php
     *
     * @param string $fileName camiho completo para um arquivo
     * @return string
     */
    public static function cleanFile($fileName) {
        return php_strip_whitespace($fileName);
    }

}

?>