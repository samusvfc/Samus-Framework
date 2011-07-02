<?php

/**
 * Inclui automaticamente um arquivo dentro do include_path não é preciso especi
 * especificar o diretorio onde ele será buscado
 *
 * @author Vinicius Fiorio - Samusdev@gmail.com
 */
class LEGADOUtil_AutoRequire {

    public static $find = false;
    public static $blockedDirs = array(".svn", "views_c");
    private static $sugests = false;
    private static $currentPath;
    private static $classNameDirSeparator = "_";
    private static $classExtension = ".php";

    public static function requireFromName($className) {

        $array = explode(self::$classNameDirSeparator, $className);
        $filename = "";

        for ($i = 0; $i < count($array) - 1; ++$i) {
            $filename .= lcfirst($array[$i]) . '/';
        }

        $filename .= $className . self::$classExtension;

        if (!class_exists($className)) {
            require_once $filename;
            return true;
        } else {
            return false;
        }
    }

    /**
     * Busca um arquivo dentro de todos diretórios específicados no include_path
     *
     * @param string $fileName
     * @return boolean
     */
    public static function requireFile($fileName) {
        self::$find = false;

        if (!self::$find) {

            $paths = explode(PATH_SEPARATOR, get_include_path());
            $find = false;
            foreach ($paths as $path) {
                self::$currentPath = $path;
                self::searchInDir($path, $fileName);

                if (self::$find)
                    break;
            }
        }
        return self::$find;
    }

    /**
     * Inclui uma classe qualquer buscando pela extensão de arquivo default de
     * classes no PHP
     *
     * @param string $className
     * @param string $defaultClassExtension .php
     * @return boolean
     */
    public static function requireClass($className, $defaultClassExtension=".php") {

        //se a classe ja existir nem inclui
        if (!class_exists($className)) {
            return self::requireFile($className . $defaultClassExtension);
        } else {
            return true;
        }
    }

    /**
     * Método recursivo que busca o arquivo em todas as pastadas dentro do
     * diretorio específicado
     *
     * @param string $dir
     * @param string $file
     * @return boolean
     */
    public static function searchInDir($dir, $file) {
        if (is_file($dir . "/" . $file)) {

            require_once $dir . "/" . $file;

            self::$find = true;

            if (self::$sugests) {
                $fileName = $dir . "/" . $file;

                $fileName = str_replace(self::$currentPath, "", $fileName);

                echo "require_once '" . $fileName . "';<br>\n";
            }

            return true;
        }

        if (is_dir($dir)) {
            $d = new DirectoryIterator($dir);

            while (!self::$find && $d->valid()) {

                if (is_dir($d->getPath() . '/' . $d->getFilename())) {
                    //testa se o arquivo pode ser incluido
                    $inc = true;
                    foreach (self::$blockedDirs as $bDir) {
                        if ($d->getFilename() == $bDir) {
                            $inc = false;
                            break;
                        }
                    }

                    if (!$d->isDot() && $inc) {

                        self::searchInDir($d->getPath() . '/' . $d->getFilename(), $file);
                    }
                }

                $d->next();
            }
        }
        return self::$find;
    }

    public static function readFiles($file) {
        $d = new DirectoryIterator($path);
    }

    /**
     * Especifica se devem ser exibidas sugestões de includes para serem adicionados
     * nos arquivos (isso melhora o desempenho)
     *
     */
    public static function showSugests() {
        self::$sugests = true;
    }

}
?>