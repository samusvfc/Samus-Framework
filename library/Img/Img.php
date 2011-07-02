<?php

/**
 * Faz operações importantes com imagens para depois realizar gravação e etc
 * 
 * 
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version v 1.0.1 23/07/2008
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category Gerenciamento de Imagens
 */
class Img {

    /**
     * Formato do arquivo
     * @var string
     */
    protected $tipo;
    /**
     * Largura do arquivo
     * @var int
     */
    protected $largura = 750;
    /**
     * Altura do arquivo que será salvo
     * @var int
     */
    protected $altura = 540;
    /**
     * Qualidade da gravação ou exibição da imagem
     * @var <type>
     */
    protected $qualidade = 90;
    /**
     * Espaço que o PHP seta para gerar uma imagem
     * @var mixed
     */
    protected $imgResource; //um espaço de imagem

    public function __construct() {

    }

    /**
     * Seta o tipo da imagem, por default é .jpg
     * @param string $fileName
     * @return string
     */
    public function setTipo($fileName) {
        $fileName = strtolower($fileName);
        $this->tipo = strtolower($fileName);
        $this->tipo = substr($fileName, - 4, 4);
        if ($this->tipo == 'jpeg')
            $this->tipo = ".jpg";
        return $this->tipo;
    }

    /**
     * Seta os valores MÁXIMOS que os arquivos devem ser salvos, setando um limite
     * maximo de largura e um limite maximo de altura, servem tanto para exibição quanto
     * para salvar
     * @param int $larguraMaxima
     * @param int $alturaMaxima
     */
    public function setTamanho($larguraMaxima = '', $alturaMaxima = '') {
        $this->largura = $larguraMaxima;
        if (empty($larguraMaxima))
            $this->largura;
        if (empty($alturaMaxima)) {
            $this->altura = (float) $larguraMaxima * 0.75;
        } else {
            $this->altura = $alturaMaxima;
        }
    }

    /**
     * Qualidade de graavação ou exibição da imagem
     * @param int $qualidade
     */
    public function setQualidade($qualidade = 90) {
        $this->qualidade = $qualidade;
    }

    public function getLargura() {
        return $this->largura;
    }

    public function setLargura($largura) {
        $this->largura = $largura;
    }

    public function getAltura() {
        return $this->altura;
    }

    public function setAltura($altura) {
        $this->altura = $altura;
    }

}