<?php

/**
 * Classe Xml_PagVars
 *
 * @author Vinicius
 */
class Xml_PagVars {

    private static $dir = 'upload/pagVars/';

    /**
     * Obtem as variáveis de um arquivo PagVar em formato de array
     * @param string $fileName nome do arquivo sem o diretorio
     * @return array
     */
    public static function getVarArray($fileName) {
        $realFileName = self::getDir() . $fileName;
        $vars = array();
        try {
            $interator = new SimpleXMLIterator($realFileName, NULL, TRUE);

            foreach ($interator as $key => $item) {
                $vars[$key] = trim(utf8_decode((string) $item));
            }
        } catch (Exception $ex) {
            throw new Exception($ex->getMessage());
        }

        return $vars;
    }

    public static function getDir() {
        return WEB_DIR . self::$dir;
    }

}