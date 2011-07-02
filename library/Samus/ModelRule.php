<?php

/**
 * Classe responsavel pela criaчуo de regras para o carregamento de modelos,
 * os principais metodos da classe DAO_CRUD tem seus respectivos metods que
 * ipoem regras ao seu carregamento,
 *
 * Toda classe de Rule, deve extgender Samus_ModelRules
 *
 * @author samus
 */
abstract class ModelRule implements Samus_ModelRulesInterface {

    public function newInstanceRule();

    public function saveRule();

    public function loadRule();

    public function deleteRule();

}