<?php
require_once 'Img/WideImage/WideImage.php';

/**
 * @author Vinicius Fiorio - samusdev@gmail.com
 */
class Img_Controller extends Samus_Controller {

    public function index() {
        error_reporting(E_ERROR);

        $tipo = '';
        $largura = Samus_Keeper::getUrl(2);
        $altura = Samus_Keeper::getUrl(3);
        $tipo = Samus_Keeper::getUrl(4);
        $nome = Samus_Keeper::getUrl(1);

        if (isset($tipo) && ($tipo != 'inside' && $tipo != 'outside' && $tipo != 'fill')) {
            $tipo = 'inside';
        }
        

        $array = explode('/', $nome);
        $filename = $array[count($array) - 1];
        $filename = 'mini_' . $largura . 'x' . $altura . '_' . $filename;

        $urlNome = APP_URL . 'upload/mini/' . $filename;
        $filename = WEB_DIR . 'upload/mini/' . $filename;


        if (file_exists($filename)) {
            $this->exibirImagemSemRedimensionar($urlNome);
        } else {


            WideImage::load($nome)
                    ->resize($largura, $altura, $tipo)
                    ->saveToFile($filename);

            $this->exibirImagemSemRedimensionar($urlNome);
        }

        exit();
        $this->setTemplateFile(false);
    }

    private function exibirImagemSemRedimensionar($filename) {
        /*
          WideImage::load($filename)
          ->resize($largura, $altura)
          ->output('jpg');
         */

        header('Location: ' . $filename);
        exit;
        //echo file_get_contents($filename);
    }

}