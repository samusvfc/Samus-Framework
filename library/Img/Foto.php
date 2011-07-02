<?php
require_once "foto/Img.php";
/**
 * Faz operações com Imagens
 *
 *
 * @author Vinicius Fiorio Custodio - Samusdev@gmail.com
 * @version v 1.0.1 23/07/2008
 * @copyright GPL - General Public License
 * @license http://www.gnu.org
 * @link http://www.Samus.com.br
 * @category GRID
 */
class Img_Foto extends Img {

    /**
     * Cor da imgResource
     * @var mixed
     */
    private $color;

    const MIME_JPG = "image/jpeg";

    const MIME_GIF = "image/gif";

    const MIME_PNG = "image/png";

    private static $imgIncFile = "img.inc.php";


    /**
     * Constrou um imgResource a partir de um nome de arquivo
     * @param string $filename
     */
    public function setImgResource($filename) {
        $this->setTipo($filename);

        $largura = $this->largura;
        $altura = $this->altura;

        list ($largura_orig, $altura_orig) = getimagesize($filename);

        if($largura && ($largura_orig < $altura_orig)) {
            $largura = ($altura / $altura_orig) * $largura_orig;
        } else {
            $altura = ($largura / $largura_orig) * $altura_orig;
        }

        $maior = max($largura, $altura);
        $menor = min($largura, $altura);
        $diferenca = $maior - $menor;

        $diferencaPorcentagem = ($diferenca * 100) / $maior;

        if($diferencaPorcentagem < 20) {
            $largura = $largura * 0.95;
            $altura = $altura * 0.95;
        }

        if($this->tipo == ".jpg") {
            $espaco = imagecreatetruecolor($largura, $altura);
            $source = imagecreatefromjpeg($filename);

            imagecopyresampled(
                    $espaco,
                    $source,
                    0,
                    0,
                    0,
                    0,
                    $largura,
                    $altura,
                    $largura_orig,
                    $altura_orig);


        } elseif($this->tipo == ".gif") {
            $espaco = imagecreatetruecolor($largura, $altura);
            $source = imagecreatefromgif($filename);
            imagecopyresampled(
                    $espaco,
                    $source,
                    0,
                    0,
                    0,
                    0,
                    $largura,
                    $altura,
                    $largura_orig,
                    $altura_orig);
        } elseif($this->tipo == ".png") {
            $espaco = imagecreatetruecolor($largura, $altura);
            $source = imagecreatefrompng($filename);
            imagecopyresampled(
                    $espaco,
                    $source,
                    0,
                    0,
                    0,
                    0,
                    $largura,
                    $altura,
                    $largura_orig,
                    $altura_orig);
        }
        $this->imgResource = $espaco;
    }

    /**
     * Seta uma cor qualquer para textos nas imagens e outras operações
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function setColor($red = 255, $green = 255, $blue = 255) {
        $this->color = imagecolorallocate(
                $this->imgResource,
                $red,
                $green,
                $blue);
    }

    public function exibirImagemSemRedimensionar($filename) {
        return file_get_contents($filename);
    }

    /**
     * RETORNA UMA IMAGEM REDIMENSIONANDA
     * Exemplo de usuo:
     *
     * Em um arquivo img.php:
     *
     * require_once "packs/foto/Img_Foto.php"
     * $foto = new Img_Foto();
     * $foto->setTamanho(550);
     * $foto->exibirImagem($_GET['img']);
     *
     *
     *
     * onde desejar usar a imagem insira:		 *
     * <img src='img.php?img=foto.jpg&l=200&a=120' alt='minha imagem' />
     * @param string $filename
     */
    public function exibirImagem($filename = '' , $miniFileName='') {

        if(! is_file($filename)) {
            throw new Exception("O caminho específicado não é um arquivo");
        } else {

            $imageSize = getimagesize($filename);

            if($imageSize["mime"] == self::MIME_JPG || $imageSize["mime"] == self::MIME_GIF || $imageSize["mime"] == self::MIME_PNG) {

                if(empty($this->imgResource)) {
                    $this->setImgResource($filename);
                }



                if(! is_resource($this->imgResource))
                    throw new Exception("Arquivo de imagem inválido");
                if($this->tipo == ".jpg") {
                    header('Content-Type: ' . self::MIME_JPG);
                    if(!empty($miniFileName)) {
                        imagejpeg($this->imgResource, $miniFileName, $this->qualidade);
                    }
                    imagejpeg($this->imgResource, null, $this->qualidade);


                } elseif($this->tipo == ".gif") {
                    header('Content-Type: ' . self::MIME_GIF);
                    imagegif($this->imgResource);
                } elseif($this->tipo == ".png") {
                    header('Content-Type: ' . self::MIME_PNG);
                    imagealphablending($this->imgResource, true);
                    imagesavealpha($this->imgResource, true);
                    imagepng($this->imgResource);
                }
                imagedestroy($this->imgResource);
            } else {
                throw new Exception(
                "O arquivo especificado não é uma umagem válida");
                return "";
            }
            return "";
        }
    }

    /**
     * Seta um texto para aparecer na imagem
     * @param string $texto
     * @param int $x posição x do texto na foto
     * @param int $y posição y do texto na foto
     * @param int $fonte fontes instaladas 1, 2, 3, 4, 5...
     */
    public function setImgTexto($texto, $x = 0, $y = 0, $fonte = 4) {
        if(empty($this->color))
            $this->setColor();
        imagestring($this->imgResource, $fonte, $x, $y, $texto, $this->color);
    }

    /**
     * Exibe uma imagem com texto
     * @param string $caminho
     * @param int $largura
     * @param int $altura
     * @param string $alt
     * @param string $texto
     * @param int $cor_r
     * @param int $cor_g
     * @param int $cor_b
     * @param int $texto_x
     * @param int $texto_y
     * @return string "<img src='img.php?img=$caminho&l=$largura&a=$altura' alt='$alt' />"
     */
    public function exibirImgComTexto($caminho, $largura = '', $altura = '',
            $alt = '', $texto = '', $cor_r = '', $cor_g = '', $cor_b = '', $texto_x = '',
            $texto_y = '') { //retorna uma imagem
        if(empty($alt))
            $alt = $caminho;
        return "<img src='img.php?img=$caminho&l=$largura&a=$altura&texto=$texto&r=$cor_r&g=$cor_g&b=$cor_b&x=$texto_x&y=$texto_y' alt='$alt' />";
    }

    /**
     * Exibe uma imagem na tela, busca o arquivo padrão 'img.php' com as devidas
     * declarações para exibição da imagem
     * @param string $caminho
     * @param string $largura
     * @param string $altura
     * @param string $alt
     * @return string "<img src='img.php?img=$caminho&l=$largura&a=$altura' alt='$alt' />"
     */
    public static function exibirImg($caminho, $largura = '', $altura = '',
            $alt = '', $cssClass = '', $parametrosAdicionais = '', $href = "") {

        if(empty($alt))
            $alt = substr($caminho, 0, 15);

        $imageSize = getimagesize($caminho);
        if($imageSize["mime"] == self::MIME_JPG || $imageSize["mime"] == self::MIME_GIF || $imageSize["mime"] == self::MIME_PNG) {

            if(! empty($cssClass))
                $cssClass = "class='$cssClass'";

            if($href) {
                $linkIni = "<a href='$href'>";
                $linkEnd = "</a>";
            }
            return " $linkIni<img src='" . self::getImgIncFile() . "-$caminho-$largura-$altura' alt='$alt' $cssClass $parametrosAdicionais />$linkEnd";
        } else {
            return "";
        }

    }

    /**
     * Especifica o arquivo base que processará as imagens
     *
     * @param $imgIncDir
     * @return string
     */
    public static function setImgIncFile($imgIncFile) {
        self::$imgIncFile = $imgIncFile;
    }

    /**
     * Obtem o arquivo de imagem
     * @return string
     */
    public static function getImgIncFile() {
        return self::$imgIncFile;
    }

}
?>