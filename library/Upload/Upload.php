<?php
/**
 * Realiza o upload de Arquivos, os arquivos não sao alterados apenas armazenados
 *
 *
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version v 1.0.1 23/07/2008
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category Upload de imagens
 */
class Upload {

    /**
     * Diretorio em que vai ser gravado o arquivo
     * @var string
     */
    private $diretorio = '../uploads/';


    /**
     * Altera o nome do arquivo para um nome unico
     * @var boolean
     */
    private $alterarNome = false;


    /**
     * Formato do arquivo
     * @var string
     */
    private $tipo;

    /**
     * Formatos permitidos se os formatos permitidos estiverem vazios ele aceita qualquer formato
     * @var array
     */
    private $formatosPermitidos = array();


    /**
     * Nome do arquivo
     * @var string
     */
    private $fileName;


    /**
     * Registra o nome original do arquivo mesmo depois de salvar
     *
     * @var string
     */
    private $nomeOriginal;

    /**
     * Prefixo usado para renomeação dos arquivos
     * @var string
     */
    private $newNamePrefix;

    private $contador = 0;

    /**
     *
     * @param string $diretorio
     * @param string $altterarNome
     */
    public function Upload($diretorio = "", $altterarNome = false) {
        $this->setDiretorio($diretorio);
        $this->alterarNome = $altterarNome;
    }

    /**
     * Seta o tipo de arquivo a partir do nome do arquivo que foi enviado
     * @param string $campo
     */
    private function setFormFileTipo($campo) {
        $this->tipo = substr($_FILES[$campo]['name'], - 4, 4);
        if(substr($this->tipo, 0, 1) != ".")
            $this->tipo = "." . $this->tipo;
        $this->tipo = strtolower($this->tipo);
    }

    /**
     * Valida os formatos permitidos
     * @param string $tipo
     * @return boolean
     */
    public function validarFormato($tipo = '') {
        if(empty($tipo))
            $tipo = $this->tipo;
        foreach($this->formatosPermitidos as $formato) {

            if($formato == $tipo)
                return true;
        }
        return false;
    }

    /**
     * Grava o arquivo no diretorio específicado
     * @param string $campo
     * @return string
     */
    public function gravarArquivo($campo) {
        $util = new Util();
        $this->setFormFileTipo($campo);

        if(! empty($this->formatosPermitidos)) { //valida o formato se ele foi setado
            if(! $this->validarFormato('.'.Util::getFileTypeByFileName($_FILES[$campo]['name'])))
                $util->erroVolta("Formato de arquivo não permitido");
        }


        if($_FILES[$campo]['error'] > 0) {
            switch ($_FILES[$campo]['error']) {
                case 1:
                    $util->erroVolta('O arquivo é maior do que o limite definido pelo servidor web<br>');
                    break;
                case 2:
                    $util->erroVolta('O arquivo é maior do que o limite definido pelo sistema<br>');
                    break;
                case 3:
                    $util->erroVolta('O upload do arquivo foi feito parcialmente');
                    break;
                case 4:
                    $util->erroVolta('Não foi feito o upload do arquivo');
                    break;
            }

            exit();

        }

        $this->nomeOriginal = $_FILES[$campo]['name'];

        if($this->alterarNome)
            $nome = $this->nomeUnico();
        else
            $nome = $this->nomeOriginal;

        if(! move_uploaded_file($_FILES[$campo]['tmp_name'], $this->diretorio . $nome))
            $util->erroVolta("ERRO ! Possivel ataque de upload! Verifique se o diretorio especificado existe e se tem permissão de escrita");

        return $nome;
    }

    /**
     * Grava o arquivo no diretorio específicado
     * @param string $campo
     * @return string
     */
    public function gravarArquivoArray($campo) {

            $util = new Util();
            $this->setFormFileTipo($campo);

            if(! empty($this->formatosPermitidos)) { //valida o formato se ele foi setado
                if(! $this->validarFormato('.'.Util::getFileTypeByFileName($_FILES[$campo]['name'])))
                    $util->erroVolta("Formato de arquivo não permitido");
            }


            if($_FILES[$campo]['error'] > 0) {
                switch ($_FILES[$campo]['error']) {
                    case 1:
                        $util->erroVolta('O arquivo é maior do que o limite definido pelo servidor web<br>');
                        break;
                    case 2:
                        $util->erroVolta('O arquivo é maior do que o limite definido pelo sistema<br>');
                        break;
                    case 3:
                        $util->erroVolta('O upload do arquivo foi feito parcialmente');
                        break;
                    case 4:
                        $util->erroVolta('Não foi feito o upload do arquivo');
                        break;
                }

                exit();

            }

            $this->nomeOriginal = $_FILES[$campo]['name'];

            if($this->alterarNome)
                $nome = $this->nomeUnico();
            else
                $nome = $this->nomeOriginal;

            if(! move_uploaded_file($_FILES[$campo]['tmp_name'], $this->diretorio . $nome))
                $util->erroVolta("ERRO ! Possivel ataque de upload! Verifique se o diretorio especificado existe e se tem permissão de escrita");


        return $nome;
    }

    /**
     * Nome do diretorio onde os arquivos serão salvos
     * @return string
     */
    public function getDiretorio() {
        return $this->diretorio;
    }

    /**
     * Seta o nome do diretorio onde os arquivos serão salvos
     * @param string $caminhoDiretorio
     */
    public function setDiretorio($caminhoDiretorio) {
        if(substr($caminhoDiretorio, - 1, 1) != "/")
            $caminhoDiretorio = $caminhoDiretorio . "/";
        if(! is_dir($caminhoDiretorio))
            @mkdir($caminhoDiretorio);
        $this->diretorio = $caminhoDiretorio;
    }

    /**
     * Retorna o nome unico para o arquivo a partir da hora
     * @return string
     */
    private function nomeUnico() {
        $data = date('ymdHis');
        $data .= $this->contador;
        return  $this->getNewNamePrefix(). $data . $this->tipo;
    }

    /**
     * Seta os formatos permitidos
     * @param string $formato1
     */
    public function setFormatosPermitidos() {
        $args = func_get_args();
        foreach($args as $arg) {
            if(substr($arg, 0, 1) != '.')
                $arg = '.' . $arg;
            array_push($this->formatosPermitidos, $arg);
        }
    }

    /**
     * Retorna os tipos permitidos de arquivo
     * @return string
     */
    public function getFormatosPermitidos() {
        return $this->formatosPermitidos;
    }


    /**
     * Obtem o nome original do arquivo
     * @return string
     */
    public function getNomeOriginal() {
        return $this->nomeOriginal;
    }


    public function getNewNamePrefix() {
        return $this->newNamePrefix;
    }

    public function setNewNamePrefix($newNamePrefix) {
        $this->newNamePrefix = $newNamePrefix;
    }

}


?>

