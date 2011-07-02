<?php

function smarty_function_img($params, &$smarty) {

    $src = $params["src"];

    /*
    if(is_file($src)) {
        $largura = $params['width'];
        $altura = $params['height'];

        list ($largura_orig, $altura_orig) = @getimagesize($src);

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
    }*/
    
        $array = explode('/', $src);
        $filename = $array[count($array) - 1];
        $filename = 'mini_' . $params['width'] . 'x' . $params['height'] . '_' . $filename;

        $urlNome = APP_URL . 'upload/mini/' . $filename;
        $filename = WEB_DIR . 'upload/mini/' . $filename;

        $id = "";
        if(isset($params['id'])) {
            $id = "id='".$params['id']."'";
        }

        if(is_file($filename)) {
            return "<img src='".$urlNome."' $id alt='".$params['alt']."' title='".$params['title']."' class='".$params['class']."' style='".$params['style']."' />" ;
        } else {
            return "<img src='../img-$src-".$params['width']."-".$params['height']."-".$params['tipo']."' $id width='$largura' height='$altura' alt='".$params['alt']."' class='".$params['class']."' style='".$params['style']."'  title='".$params['title']."' />" ;
        }

    


}
?>
