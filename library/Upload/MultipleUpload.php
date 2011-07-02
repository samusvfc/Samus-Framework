<?php


/**
 * Realiza o upload multiplo através de forms
 *
 * @author samus
 */
class Upload_MultipleUpload {

    /**
     * Array dos formatos permitods SEM PONTO
     * @var array
     */
    private $allowedFormats = array();

    /**
     * Caminho para o diretório que receberá os arquvos
     * @var string
     */
    private $dir = "./";

    /**
     * Prefixo para o nome dos arquivos que serão renomeados
     * @var string
     */
    private $newNamePrefix;

    /**
     * Define se os arquivos serão renomeados
     * @var boolean
     */
    private $renameFiles = true;

    /**
     * Nome do campo do formulário com os arquivos
     * @var string
     */
    private $inputName;

    /**
     * Array com o nome original dos arquivos
     * @var array
     */
    private $originalNames = array();

    /**
     * Conta o numero de arquivos salvos
     * @var int
     * @static
     */
    private static $countFiles = 1;

    public function __construct($inputName , $targetDirectory , $renameFiles=true) {
        $this->setRenameFiles($renameFiles);
        $this->setDir($targetDirectory);
        $this->setInputName($inputName);

    }

    public function save() {

        foreach($_FILES[$this->inputName]['error'] as $error) {
            if($error > 0) {
                switch ($error) {
                    case 1:
                        throw new Upload_UploadException('O arquivo é maior do que o limite definido pelo servidor web');
                        break;
                    case 2:
                        throw new Upload_UploadException('O arquivo é maior do que o limite definido pelo sistema<br>');
                        break;
                    case 3:
                        throw new Upload_UploadException('O upload do arquivo foi feito parcialmente');
                        break;
                    case 4:
                        throw new Upload_UploadException('Não foi feito o upload do arquivo');
                        break;
                }
            }
        }

        $savedFilesNames = array();

        foreach($_FILES[$this->inputName]['name'] as $key => $name) {

            $this->originalNames[] = $name;

            if($this->renameFiles) {
                $nameOfFile = $this->uniqueName($name);
            } else {
                $nameOfFile = $name;
            }


            if(! move_uploaded_file($_FILES[$this->inputName]['tmp_name'][$key], $this->getDir() . $nameOfFile)) {
                throw new Upload_UploadException("ERRO ! Possivel ataque de upload! Verifique se o diretorio especificado existe e se tem permissão de escrita");
            }

            $savedFilesNames[] = $nameOfFile;

            self::$countFiles++;

        }

        return $savedFilesNames;

    }


    /**
     * Retorna o nome unico para o arquivo a partir da hora
     * @return string
     */
    private function uniqueName($originalFileName) {
        $data = date(ymdHis);
        $data .= self::$countFiles;
        return  $this->getNewNamePrefix(). $data . '.'.  Util::getFileTypeByFileName($originalFileName);
    }


    /**
     * Seta o tipo de arquivo a partir do nome do arquivo que foi enviado
     * @param string $campo
     */
    private function getFormFileType($originalFileName) {

        return Util::getFileTypeByFileName($originalFileName);

        $this->tipo = substr($originalFileName, - 4, 4);
        if(substr($this->tipo, 0, 1) != ".")
            $this->tipo = "." . $this->tipo;
        $this->tipo = strtolower($this->tipo);
    }





    ////////////////////////////////////////////////////////////////////////////
    // GETTERS AND SETTERS
    ////////////////////////////////////////////////////////////////////////////

    public function getAllowedFormats() {
        return $this->allowedFormats;
    }

    public function setAllowedFormats($allowedFormats) {
        $this->allowedFormats = $allowedFormats;
    }

    public function getDir() {
        return $this->dir;
    }

    public function setDir($dir) {
        $this->dir = $dir;
    }

    public function getNewNamePrefix() {
        return $this->newNamePrefix;
    }

    public function setNewNamePrefix($newNamePrefix) {
        $this->newNamePrefix = $newNamePrefix;
    }

    public function getRenameFiles() {
        return $this->renameFiles;
    }

    public function setRenameFiles($renameFiles) {
        $this->renameFiles = $renameFiles;
    }

    public function getInputName() {
        return $this->inputName;
    }

    public function setInputName($inputName) {
        $this->inputName = $inputName;
    }

public function getOriginalNames() {
    return $this->originalNames;
}






}
?>
