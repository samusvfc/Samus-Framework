<?php
require_once 'FreteExecption.php';
/**
 * Calcula o frete de um item qualquer conforme cep destino e origem e servi�o
 *
 * @author Vinicius
 */
class Frete_Correios {
    const FRETE_PAC = '41106';
    const FRETE_SEDEX = '40010';
    const FRETE_SEDEX_10 = '40215';
    const FRETE_SEDEX_HOJE = '40290';
    const FRETE_E_SEDEX = '81019';
    const FRETE_MALOTE = '44105';

    /**
     * Calcula o frete do produto conforme os parametros, metodo baseado na fun��o
     * do link:
     * http://www.phpavancado.net/node/431
     *
     * @param string $servico Numero do servi�o
     * @param string $cepOrigem cep de origem
     * @param string $cepDestino cep destino
     * @param string $peso peso em gramas
     * @return array
     */
    public function calcularFreteSimples($servico, $cepOrigem, $cepDestino, $peso) {
        if (!$sock = fsockopen('www.correios.com.br', 80, $errornro, $error, 60)) {
            throw new Exception($error, $errornro);
        }

        $msg = "GET /encomendas/precos/calculo.cfm?"
                . "Servico={$servico}&cepOrigem={$cepOrigem}&cepDestino={$cepDestino}"
                . "&peso={$peso}&resposta=localhost HTTP/1.1\n"
                . "Host: www.correios.com.br\nConnection: Close\n\n";

        fwrite($sock, $msg);

        while (!feof($sock)) {
            $line = fgets($sock);
            if (!preg_match('/^Location: \w+\?(.*)$/i', $line, $match))
                continue;

            $data = array();
            foreach (split('&', $match[1]) as $item) {
                $t = split('=', $item);
                $data[$t[0]] = trim($t[1]);
            }

            break;
        }
        $data['Servico'] = urldecode($data['Servico']);
        $data['erro'] = urldecode($data['erro']);

        return $data;
    }

    const FRETE_PAC_SEM_CONTRATO = '41106';
    const FRETE_SEDEX_SEM_CONTRATO = '40010';
    const FRETE_SEDEX_A_COBRAR_SEM_CONTRATO = '40045';
    const FRETE_SEDEX_10_SEM_CONTRATO = '40215';
    const FRETE_SEDEX_HOJE_SEM_CONTRATO = '40290';
    const FRETE_SEDEX_COM_CONTRATO = '40096';
    const FRETE_E_SEDEX_COM_CONTRATO = '81019';
    const FRETE_PAC_COM_CONTRATO = '41068';

    const FORMAT_CAIXA = 1;
    const FORMATO_PACOTE = 1;
    const FORMATO_PRISMA = 2;
    const SIM = 'S';
    const NAO = 'N';
    const RETORNO_TIPO_XML = 'XML';
    const RETORNO_TIPO_POPUP = 'Popup';

    /**
     * Seu c�digo administrativo junto � ECT. O c�digo est� dispon�vel no corpo do contrato firmado com os Frete_Correios.
     * N�o obrigat�rio
     * @var string
     */
    public $codEmpresa;
    /**
     * Senha para acesso ao servi�o, associada ao seu c�digo administrativo. A senha inicial corresponde aos 8 primeiros d�gitos do CNPJ informado no contrato.
     * @var string
     */
    public $dsSenha;
    /**
     * C�digo do servi�o:
     * Estes c�digo s�o descritos nas contaantes da classe
     *
     * 41106 - PAC sem contrato
     * 40010 - SEDEX sem contrato
     * 40045 - SEDEX a Cobrar, sem contrato
     * 40215 - SEDEX 10, sem contrato
     * 40290 - SEDEX Hoje, sem contrato
     * 40096 - SEDEX com contrato
     * 40436 - SEDEX com contrato
     * 40444 - SEDEX com contrato
     * 81019 - e-SEDEX, com contrato
     * 41068 - PAC com contrato
     * @var string
     */
    public $codigoServico;
    /**
     * CEP de Origem sem h�fen.Exemplo: 05311900
     * Obrigat�rio
     * @var string
     */
    public $cepOrigem;
    /**
     * CEP de Destino Sem h�fem
     * Obrigat�rio
     * @var string
     */
    public $cepDestino;
    /**
     * Peso da encomenda, incluindo sua embalagem. O peso deve ser informado em quilogramas.
     * Obrigat�rio
     * @var float
     */
    public $peso;
    /**
     * Formato da encomenda (incluindo embalagem).
     * Valores poss�veis: 1 ou 2
     * 1 ? Formato caixa/pacote
     * 2 ? Formato rolo/prisma
     * @var int
     */
    public $formato = 1;
    /**
     * Comprimento da encomenda (incluindo embalagem), em cent�metros.
     * @var float
     */
    public $comprimento = 1;
    /**
     * Altura da encomenda (incluindo embalagem), em cent�metros.
     * @var float
     */
    public $altura = 1;
    /**
     * Largura da encomenda (incluindo embalagem), em cent�metros.
     * @var string
     */
    public $largura = 1;
    /**
     * Di�metro da encomenda (incluindo embalagem), em cent�metros.
     * Obs: Sim, para PAC, Se o servi�o n�o exigir medidas informar zero.
     * @var float
     */
    public $diametro = '0';
    /**
     * Indica se a encomenda ser� entregue com o servi�o adicional m�o pr�pria.
     * Valores poss�veis: S ou N (S ? Sim, N ? N�o)
     * @var string
     */
    public $maoPropria = 'N';
    /**
     * Indica se a encomenda ser� entregue com o servi�o adicional valor declarado.
     * Neste campo deve ser apresentado o valor declarado desejado, em Reais.
     * @var float
     */
    public $valorDeclarado = '0';
    /**
     * Indica se a encomenda ser� entregue com o servi�o adicional aviso de recebimento.
     * Valores poss�veis: S ou N (S ? Sim, N ? N�o)
     * @var string
     */
    public $avisoRecebimento = 'N';
    /**
     * Indica a forma de retorno da consulta.
     * XML - Resultado em XML
     * Popup - Resultado em uma janela popup
     * <URL> - Resultado via post em uma p�gina do requisitante
     * @var string
     */
    public $retorno = 'XML';




    /**
     * Calcula o frete do produto conforme os parametros informados
     * http://www.correios.com.br/servicos/precos_tarifas/pdf/SCPP_manual_implementacao_calculo_remoto_de_precos_e_prazos.pdf
     * @return string
     */
    public function calcularFrete() {

        if($this->codigoServico == Frete_Correios::FRETE_PAC) {
            if( (float) $this->largura < 11) {
                $this->largura = 11;
            }

            if((float) $this->comprimento < 16) {
                $this->comprimento = 16;
            }
            
            if((float) $this->altura < 2) {
                $this->altura = 2;
            }
        }


        $str = "http://ws.correios.com.br/calculador/CalcPrecoPrazo.aspx";
        $str .= "?nCdEmpresa=".$this->codEmpresa;
        $str .= "&sDsSenha=".$this->dsSenha;
        $str .= "&sCepOrigem=".$this->cepOrigem;
        $str .= "&sCepDestino=".$this->cepDestino;
        $str .= "&nVlPeso=".$this->peso;
        $str .= "&nCdFormato=".$this->formato;
        $str .= "&nVlComprimento=".$this->comprimento;
        $str .= "&nVlAltura=".$this->altura;
        $str .= "&nVlLargura=".$this->largura;
        $str .= "&sCdMaoPropria=".$this->maoPropria;
        $str .= "&nVlValorDeclarado=". $this->valorDeclarado ;
        $str .= "&sCdAvisoRecebimento=".$this->avisoRecebimento;
        $str .= "&nCdServico=".$this->codigoServico;
        $str .= "&nVlDiametro=".$this->diametro;
        $str .= "&StrRetorno=".$this->retorno;

        $xml = @simplexml_load_file($str);

        return $xml;
    }

}