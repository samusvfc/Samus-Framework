<?php

interface Samus_FilterInterface {

    /**
     * O filtro щ executado sempre que a classe Filtro щ executada, todas as 
     * implementaчѕes devem ser feitas no construtor e em filter
     */
    public function filter();

    /**
     * O filtro щ executado apѓs a compilaчуo e exibiчуo do template da
     * pсgina
     */
    public function endFilter();
}