<?php

//Criado em 09/03/2008 às 12:05:18 - Developer [ s a m u s ] - www.samusdev.com.br

class Xml_Rss {

    protected $rssVersao = "2.0";
    protected $encoding = "ISO-8859-1";
    protected $linguagem = "pt-BR";
    protected $titulo;
    protected $descricao;
    protected $link;
    protected $xml;

    public function __construct($titulo, $descricao, $link) {
        $this->titulo = $titulo;
        $this->descricao = $descricao;
        $this->link = $link;

        $this->xml .= '<channel>
';
        $this->xml .= "<title>$titulo</title>
            ";
        $this->xml .= "<description><![CDATA[" . htmlentities($descricao) . "]]></description>
            ";
        $this->xml .= "<link>$link</link>";
        $this->xml .= "<language>$this->linguagem</language>
            ";
    }

    public function montarItem($titulo, $descricao, $link, $data, $autor="") {
        $this->xml .= '<item>
';
        $this->xml .= "<title><![CDATA[$titulo]]></title>
            ";
        $this->xml .= "<description><![CDATA[$descricao]]></description>
            ";
        $this->xml .= "<pubDate>$data</pubDate>
            ";
        $this->xml .= "<link>$link</link>
            ";

        if ($autor)
            $this->xml .= '<author>' . $autor . '</author>';

        $this->xml .= '</item>
';
    }

    public function finalizar() {
        $this->xml .= '</channel></rss>
';
    }

    public function exibirRss() {
        echo '<?xml version="1.0" encoding="' . $this->encoding . '"?>
';
        echo '<rss version="' . $this->rssVersao . '">
';
        $this->finalizar();
        echo $this->xml;
    }

}

?>
