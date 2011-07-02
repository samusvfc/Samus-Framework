<?php

require_once 'Email/phpMailer/class.phpmailer.php';

/**
 * Classe de envio de emails da poltex, em seu contrutor são configuradas as propriedades
 * padrão de envio das mensagens do site da poltex
 *
 * @author samus
 */
class Email_Envio extends PHPMailer {

    /**
     * @var Config
     */
    public $config;

    public function __construct($useConfig = true) {
        $useConfig = false;

        $this->IsSMTP();
        $this->SMTPAuth = true;
        $this->SMTPSecure = "ssl";
        $this->Port     = "465";
        $this->Host     = "smtp.veracaser.com";
        $this->Username = "";
        $this->Password = "";
        $this->From     = "contato@veracaser.com.br";
        $this->FromName = "Vera Caser";

        $this->Subject = "Dog Hotel";
    }

    public function setAssunto($assunto) {
        $this->Subject = $assunto;
    }

    /**
     * Envia uma mensagem qualquer com um cabeçalho HTML
     *
     * @param string $mensagem
     * @return boolean
     */
    public function enviar($mensagem) {
        $this->IsHTML(true);

        error_reporting(E_ERROR);

        $msg = '
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
  <head>
    <title>' . $this->Subject . '</title>
    <meta http-equiv="Content-Type" content="text/html; charset=windows-1252">
  </head>
  <body>
  <div style=\'font-family: Tahoma, Arial, Helvetica, sans-serif;
        font-size: 11px;\'>
';
        $msg .= $mensagem;
        $msg .= "</div></body></html>";

        $this->Body = $msg;


        if ($_SERVER['SERVER_NAME'] == 'localhost') {
            $this->ClearAddresses();
            $this->AddAddress('samus@samus.com.br');
        }

        $r = $this->Send();

        error_reporting(E_ALL);

        return $r;
    }

    /**
     * Envia uma mensagem como texto
     *
     * @param string $mensagem
     * @return boolean
     */
    public function enviarTxt($mensagem) {
        error_reporting(E_ERROR);

        if ($_SERVER['SERVER_NAME'] == 'localhost') {
            $this->ClearAddresses();
            $this->AddAddress('samus@samus.com.br');
        }

        $this->IsHTML(false);
        $this->Body = $mensagem;
        $r = $this->Send();
        error_reporting(E_ALL);
        return $r;
    }

}

?>
