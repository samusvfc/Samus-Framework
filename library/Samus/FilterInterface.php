<?php

interface Samus_FilterInterface {

    /**
     * O filtro � executado sempre que a classe Filtro � executada, todas as 
     * implementa��es devem ser feitas no construtor e em filter
     */
    public function filter();

    /**
     * O filtro � executado ap�s a compila��o e exibi��o do template da
     * p�gina
     */
    public function endFilter();
}