<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Vinicius
 * Date: 08/04/11
 * Time: 19:43
 * To change this template use File | Settings | File Templates.
 */
 
class Samus_Plugin_FormCidadeEstado extends Samus_Plugin {


    //
    public $cidadesOptions;
    public $estadosOptions;
    public $cidadeSelecionada;
    public $estadoSelecinoado = 8;

    public function index() {
        
        $cidade = new Model_Endereco_Cidade();
        $estado = new Model_Endereco_Estado();

        foreach($cidade->getDao()->addAttr('id,nome')->find('estado='.$this->estadoSelecinoado,'nome ASC') as $c) {
            $this->cidadesOptions[$c['id']] = $c['nome'];
        }

        foreach($estado->getDao()->addAttr('id,nome,uf')->find('','nome ASC') as $e) {
            $this->estadosOptions[$e['id']] = $e['nome'];
        }

    }
}
